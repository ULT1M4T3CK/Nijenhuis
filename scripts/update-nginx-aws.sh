#!/bin/bash
# Update nginx config on AWS EC2 and reload. Use after changing deploy/aws/nginx-aws.conf
# (e.g. to fix /login 404 without full deploy)

set -e
AWS_IP="51.20.126.15"
AWS_USER="ec2-user"
PEM_KEY="/home/andre/Documents/Instances/PEM/ultimAItech.pem"
LOCAL_DIR="/home/andre/Desktop/Projects/Nijenhuis"
SCP_CMD="scp -i $PEM_KEY -o StrictHostKeyChecking=no"
SSH_CMD="ssh -i $PEM_KEY -o StrictHostKeyChecking=no $AWS_USER@$AWS_IP"

[ ! -f "$PEM_KEY" ] && { echo "PEM key not found: $PEM_KEY"; exit 1; }
chmod 400 "$PEM_KEY"

echo "Uploading nginx config..."
$SCP_CMD "$LOCAL_DIR/deploy/aws/nginx-aws.conf" "$AWS_USER@$AWS_IP:/tmp/"

echo "Installing and reloading nginx..."
$SSH_CMD "sudo mv /tmp/nginx-aws.conf /etc/nginx/conf.d/nijenhuis.conf && sudo nginx -t && sudo systemctl reload nginx"

echo "Done. Test: https://nijenhuis-botenverhuur.com/login"
