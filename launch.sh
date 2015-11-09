#!/bin/bash

./cleanup.sh

#making an array in bash to hold instance IDs
declare -a instanceIDArray

mapfile -t instanceIDArray < <(aws ec2 run-instances --image-id $1  --count $2 --instance-type $3 --key-name $4 --security-group-ids $5 --subnet-id $6 --iam-instance-profile Name=$7 --associate-public-ip-address --user-data file:/install-webserver.sh  --output table | grep InstanceId | sed "s/|//g" | tr -d ' ' | sed "s/InstanceId//g")

#echo the instance IDs from created EC2 instances that are within the array
echo "Instance IDs created: " ${instanceIDArray[@]}

#wait until ec2 instances are launched before continuing the script
echo "Instances are still spinning up..."
aws ec2 wait instance-running --instance-ids ${instanceIDArray[@]}

#attach ec2 instances to an elastic load balancer; a variable is created to ease echo command use
elbURL=(`aws elb create-load-balancer --load-balancer-name test --listeners Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80 --security-groups sg-2bdbe04c --subnets subnet-47b8e96c --output=text`); 
echo "Created ELB: "$elbURL
echo -e "\nFinished launching the requested ELB... Pausing for 30 seconds"
for i in {0..30}; do echo -ne '0o0'; sleep 1; done
echo -e "\n"

aws elb register-instances-with-load-balancer --load-balancer-name test --instances ${instanceIDArray[@]}

aws elb configure-health-check --load-balancer-name test --health-check Target=HTTP:80/index.html,Interval=30,UnhealthyThreshold=2,HealthyThreshold=2,Timeout=3

echo -e "\nWaiting 3 minutes (180 seconds) before creating autoscaling groups"
for i in {0..180}; do echo -ne '0o0'; sleep 1; done

#creating the launch configuration/autoscaling groups
aws autoscaling create-launch-configuration --launch-configuration-name itmo-444-launch-config --image-id ami-d05e75b8 --key-name ITMO-444-Fall2015  --security-groups sg-2bdbe04c --instance-type t2.micro --user-data file://install-webserver.sh --iam-instance-profile EC2Creator

aws autoscaling create-auto-scaling-group --auto-scaling-group-name itmo-444-auto-scaling-group --launch-configuration-name itmo-444-launch-config --load-balancer-names test  --health-check-type ELB --min-size 3 --max-size 6 --desired-capacity 3 --default-cooldown 600 --health-check-grace-period 120 --vpc-zone-identifier subnet-47b8e96c

aws cloudwatch put-metric-alarm --alarm-name AddCapacity --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 120 --threshold 30 --comparison-operator GreaterThanOrEqualToThreshold --dimensions "Name=AutoScalingGroupName,Value=itmo-444-add-asg" --evaluation-periods 2

aws cloudwatch put-metric-alarm --alarm-name RemoveCapacity --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 120 --threshold 10 --comparison-operator LessThanOrEqualToThreshold --dimensions "Name=AutoScalingGroupName,Value=itmo-444-subtract-asg" --evaluation-periods 2 

./install-env.sh
