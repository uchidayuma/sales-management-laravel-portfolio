# AWS IAM User Setup for Terraform

Since you are currently using the Root Account, **it is highly recommended to create a dedicated IAM Admin User** for daily use and for running Terraform. The Root Account should be secured and rarely used.

## 1. Create an IAM User
1.  Log in to the **AWS Management Console** with your Root Account.
2.  Navigate to **IAM** (Identity and Access Management).
3.  Click **Users** -> **Create user**.
4.  **User details**:
    *   **User name**: `terraform-admin` (or any name you prefer).
5.  **Permissions options**:
    *   Select **Attach policies directly**.
    *   Search for and select: **`AdministratorAccess`**.
    *   *Note: For a specialized production environment, we would narrow this down, but for learning and comprehensive Terraform (creating VPCs, RDS, IAM roles, etc.), AdministratorAccess avoids permission errors.*
6.  Complete the creation process.

## 2. Create Access Keys
1.  Click on the newly created user (`terraform-admin`).
2.  Go to the **Security credentials** tab.
3.  Scroll down to **Access keys**.
4.  Click **Create access key**.
5.  Select **Command Line Interface (CLI)**.
6.  Tick the conformation box and click **Next**.
7.  Click **Create access key**.
8.  **IMPORTANT**: Download the `.csv` file or copy the **Access key ID** and **Secret access key** immediately. You cannot see the Secret key again later.

## 3. Configure AWS CLI
On your local machine (Mac), open your terminal and run:

```bash
# --profile オプションを使うことで、デフォルトの設定（~/.aws/credentialsの[default]）を上書きせずに別設定を保存できます
aws configure --profile terraform-admin
```

Enter the details:
*   **AWS Access Key ID**: [Paste from step 2]
*   **AWS Secret Access Key**: [Paste from step 2]
*   **Default region name**: `ap-northeast-1` (Tokyo)
*   **Default output format**: `json`

## 4. Verify
Test access:
```bash
aws sts get-caller-identity --profile terraform-user
```
You should see the Arn ending in `user/terraform-admin`.

## 5. Using with Terraform
When running Terraform, you can export the profile variable. This tells Terraform (and the AWS CLI) to use the `terraform-admin` credentials instead of `default` for the current session.

```bash
# This sets the profile ONLY for the current terminal session
export AWS_PROFILE=terraform-admin

# Verify the switch
aws sts get-caller-identity
# Should show the terraform-admin ARN


```bash
export AWS_PROFILE=terraform-user
terraform init
terraform apply
```
