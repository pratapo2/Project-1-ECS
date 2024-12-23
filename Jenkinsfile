pipeline {
    agent any

    environment {
        AWS_REGION = 'ap-south-1'            // AWS region
        ECR_REPO_NAME = 'project-1-ecs'      // ECR repository name
        CLUSTER_NAME = 'app'                 // ECS cluster name
        ACCOUNT = '418295679392'
        SERVICE_NAME = 'app-fargate'         // ECS service name
        GIT_URL = 'https://github.com/pratapo2/Project-1-ECS.git' // Git repository URL
        TASK_DEFINITION_FAMILY = 'app-ecs'  // Existing ECS task definition family
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

        stage('Update ECS Service') {
            steps {
                script {
                    // Step 1: Describe the ECS task definition
                    def newTaskDef = sh(script: """
                        aws ecs describe-task-definition \
                            --task-definition ${TASK_DEFINITION_FAMILY} \
                            --region ${AWS_REGION} \
                            --output json
                        """, returnStdout: true).trim()

                    // Step 2: Extract container definition and modify the image tag
                    def taskDefinitionJson = readJSON(text: newTaskDef)
                    def containerDefinitions = taskDefinitionJson.taskDefinition.containerDefinitions
                    def newImageTag = "${ACCOUNT}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPO_NAME}:${BUILD_ID}"

                    // Update the image tag in the container definition
                    containerDefinitions.each { container ->
                        if (container.name == 'app-container') { // Ensure to use the correct container name
                            container.image = newImageTag
                        }
                    }

                    // Step 3: Register a new task definition revision with the updated image tag
                    // Use writeJSON with returnText instead of JsonBuilder
                    def updatedTaskDef = writeJSON(
                        returnText: true, 
                        json: [containerDefinitions: containerDefinitions]
                    )

                    // Step 4: Register the updated task definition with AWS ECS
                    def registerTaskDefResult = sh(script: """
                        aws ecs register-task-definition \
                            --family ${TASK_DEFINITION_FAMILY} \
                            --container-definitions '${updatedTaskDef}' \
                            --region ${AWS_REGION} \
                            --output json
                        """, returnStdout: true).trim()

                    // Step 5: Extract the new task definition ARN
                    def updatedTaskDefArn = readJSON(text: registerTaskDefResult).taskDefinition.taskDefinitionArn
                    echo "Updated Task Definition ARN: ${updatedTaskDefArn}"

                    // Step 6: Update ECS service with the new task definition revision
                    sh """
                        aws ecs update-service \
                            --cluster ${CLUSTER_NAME} \
                            --service ${SERVICE_NAME} \
                            --task-definition ${updatedTaskDefArn} \
                            --force-new-deployment \
                            --region ${AWS_REGION} \
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
