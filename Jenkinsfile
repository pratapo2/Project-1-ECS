pipeline {
    agent any

    environment {
        AWS_REGION = 'ap-south-1'            
        ECR_REPO_NAME = 'project-1-ecs'     
        CLUSTER_NAME = 'app'                
        ACCOUNT = '418295679392'             
        SERVICE_NAME = 'ecs-app'          
        GIT_URL = 'https://github.com/pratapo2/Project-1-ECS.git' 
        TASK_DEFINITION_FAMILY = 'ecs-task' 
        EXECUTION_ROLE_ARN = 'arn:aws:iam::418295679392:role/ecsTaskExecutionRole' // Add execution role ARN here
    }

    stages {
        stage('Checkout Code') {
            steps {
                git(
                    url: "${GIT_URL}",
                    branch: 'main', // Branch name
                    credentialsId: 'git-credentials' // Jenkins credentials ID
                )
            }
        }

        stage('Authenticate with ECR') {
            steps {
                script {
                    sh """
                    aws ecr get-login-password --region ${AWS_REGION} | \
                    docker login --username AWS --password-stdin ${ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com
                    """
                }
            }
        }

        stage('Build and Push Docker Image') {
            steps {
                script {
                    def imageTag = "${BUILD_ID}" // Use Jenkins build ID as tag
                    def imageName = "${ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPO_NAME}:${imageTag}"
                    sh """
                    docker build -t ${imageName} .
                    docker push ${imageName}
                    """
                }
            }
        }

        stage('Register ECS Task Definition') {
            steps {
                script {
                    // Register ECS Task Definition with new image tag
                    def imageTag = "${BUILD_ID}" // Jenkins build ID as image tag
                    def repository = "${ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPO_NAME}"
                    def logGroup = "/ecs/ecs-task"

                    def registerTaskDefResult = sh(script: """
                    aws ecs register-task-definition \
                      --family ${TASK_DEFINITION_FAMILY} \
                      --execution-role-arn ${EXECUTION_ROLE_ARN} \
                      --container-definitions '[{
                        \"name\": \"app\",
                        \"image\": \"${repository}:${imageTag}\",
                        \"cpu\": 0,
                        \"memory\": 512,
                        \"memoryReservation\": 256,
                        \"portMappings\": [
                          {
                            \"containerPort\": 80,
                            \"hostPort\": 80,
                            \"protocol\": \"tcp\",
                            \"name\": \"http\",
                            \"appProtocol\": \"http\"
                          }
                        ],
                        \"essential\": true,
                        \"environment\": [],
                        \"environmentFiles\": [],
                        \"mountPoints\": [],
                        \"volumesFrom\": [],
                        \"ulimits\": [],
                        \"logConfiguration\": {
                          \"logDriver\": \"awslogs\",
                          \"options\": {
                            \"awslogs-group\": \"${logGroup}\",
                            \"mode\": \"non-blocking\",
                            \"awslogs-create-group\": \"true\",
                            \"max-buffer-size\": \"25m\",
                            \"awslogs-region\": \"${AWS_REGION}\",
                            \"awslogs-stream-prefix\": \"ecs\"
                          },
                          \"secretOptions\": []
                        },
                        \"healthCheck\": {
                          \"command\": [
                            \"CMD-SHELL\",
                            \"curl -f http://localhost || exit 1\"
                          ],
                          \"interval\": 30,
                          \"timeout\": 3,
                          \"retries\": 3,
                          \"startPeriod\": 60
                        },
                        \"systemControls\": []
                      }]' \
                      --requires-compatibilities FARGATE \
                      --network-mode awsvpc \
                      --cpu 256 \
                      --memory 512 \
                      --region ${AWS_REGION} \
                      --output json
                    """, returnStdout: true).trim()

                    // Extract the new task definition ARN
                    def updatedTaskDefArn = readJSON(text: registerTaskDefResult).taskDefinition.taskDefinitionArn
                    echo "Updated Task Definition ARN: ${updatedTaskDefArn}"

                    // Save the task definition ARN for use in the update service step
                    env.TASK_DEFINITION_ARN = updatedTaskDefArn
                }
            }
        }

        stage('Update ECS Service') {
            steps {
                script {
                    // Update ECS service with the new task definition revision
                    sh """
                        aws ecs update-service \
                            --cluster ${CLUSTER_NAME} \
                            --service ${SERVICE_NAME} \
                            --task-definition ${env.TASK_DEFINITION_ARN} \
                            --force-new-deployment \
                            --region ${AWS_REGION} \
                            --network-configuration "awsvpcConfiguration={subnets=[subnet-0b58915504701efdc,subnet-0b122b7f60fc71bd3],securityGroups=[sg-01ddebf56fa708310],assignPublicIp=ENABLED}" \
                            --output json
                    """
                }
            }
        }
    }

    post {
        success {
            echo 'Deployment successful!'
        }
        failure {
            echo 'Deployment failed!'
        }
    }
}
