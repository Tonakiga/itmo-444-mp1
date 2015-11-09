#!/bin/bash

#creating the db group to hold the subnets
aws rds create-db-subnet-group --db-subnet-group-name mp1 --db-subnet-group-description "DB subnet group for mp1" --subnet-ids subnet-47b8e96c subnet-f3606884    

aws rds create-db-instance --db-instance-identifier jss-itmo-444-db --db-instance-class db.t1.micro --engine MySQL --master-username jssdbuser --master-user-password superpassword --allocated-storage 5 --db-subnet-group-name mp1

aws rds wait db-instance-available

php ./createdatabase
