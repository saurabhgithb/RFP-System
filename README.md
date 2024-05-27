# RFP Management System v1.0

### Table of Contents

- [RFP Management System v1.0](#rfp-management-system-v10)
    - [Table of Contents](#table-of-contents)
    - [Overview](#overview)
    - [Features](#features)
    - [Installation](#installation)
    - [Usage](#usage)
      - [Login](#login)
      - [Admin Registration](#admin-registration)
      - [Vendor Registration](#vendor-registration)
      - [Forgot Password](#forgot-password)
      - [Reset Password](#reset-password)
      - [Admin Panel](#admin-panel)
      - [Vendor Panel](#vendor-panel)
    - [Email Notifications](#email-notifications)
    - [Improvements](#improvements)
    - [Contact](#contact)

### Overview

The RFP (Request for Proposal) Management System is designed to facilitate the issuance, management, and response to RFPs by companies and vendors. The system provides a structured way for companies to issue RFPs and for vendors to submit quotes. This document outlines the setup, features, and functionality of the RFP Management System.

### Features

- **User Authentication**: Separate login for admin and vendors.
- **Admin Registration**: Exclusive registration for admin users.
- **Vendor Registration**: Detailed registration for vendors, requiring admin approval.
- **Password Management**: Forgot and reset password functionalities.
- **Admin Panel**: Manage categories, vendors, RFPs, and quotes.
- **Vendor Panel**: View and respond to RFPs issued by the company.
- **Email Notifications**: Automated emails for registration, approval, and quote submissions.

### Installation

1. **Clone the repository**:
    ```bash
    git clone https://github.com/saurabhgithb/RFP-System
    cd RFP-System
    ```

2. **Install dependencies**:
    ```bash
    composer install
    ```

3. **Set up Environment Variables**:
    - Create `.env` file in the base folder and add configs mentioned in `.env.example`.
    - Create DB using the `rfp-system.sql` file in the database folder.

4. **Start the application**:
    - All configs are ready, you can now open the application on the xampp server. 

### Usage

#### Login

- **URL**: `/index.php`
- **Description**: Users can log in using their email and password. Both admin and vendors use the same login page.

#### Admin Registration

- **URL**: `/register-admin.php`
- **Description**: Sign-up page exclusively for admin users. Admins will have the ability to manage the entire RFP system.

#### Vendor Registration

- **URL**: `/register.php`
- **Description**: Detailed registration page for vendors. Vendors must provide additional information, which is stored in the `vendors_details` table. Vendors require admin approval before they can log in and participate.

#### Forgot Password

- **URL**: `/forgot-password.php`
- **Description**: Users can request a password reset link by entering their registered email.

#### Reset Password

- **URL**: `/reset-password.php`
- **Description**: Users can reset their password using the link sent to their email.

#### Admin Panel

- **Home Page**
  - **URL**: `/dashboard-admin.php`
  - **Description**: Welcome page for the admin.

- **Categories**
  - **URL**: `/categories.php`
  - **Description**: Manage RFP categories.

- **Vendors List**
  - **URL**: `/vendors.php`
  - **Description**: Approve and view registered vendors.

- **RFP List**
  - **URL**: `rfp.php`
  - **Description**: Create and manage RFPs, including closing RFPs and viewing quotes.

- **RFP Quotes**
  - **URL**: `/rfp-quotes.php`
  - **Description**: View all quotes submitted by vendors for all RFPs.

- **Admin List**
  - **URL**: `/list-admins.php`
  - **Description**: List admin users.

#### Vendor Panel

- **Home Page**
  - **URL**: `dashboard-vendor.php`
  - **Description**: Welcome page for vendors.

- **RFP List**
  - **URL**: `rfp-for-quotes-vendor.php`
  - **Description**: View and respond to assigned RFPs.

- **Edit Vendor**
  - **URL**: `edit-vendor.php`
  - **Description**: Edit vendor details.

### Email Notifications

The system sends automated emails for various actions:

- **Vendor Registration**: Confirmation email upon registration.
- **Vendor Approval**: Email notification when the vendor's account is approved.
- **RFP Creation**: Email to vendors when a new RFP is issued.
- **Quote Submission**: Email to admin when a vendor submits a quote.

### Improvements

- Directory structure could be improved.
- Url could be more structured.
- Admin pages can reside in admin folder and vendor pages can reside in vendor folder and auth pages in auth folder.

### Contact

For any issues or inquiries, please contact Velocity Software Solutions Pvt. Ltd.

- **Email**: [saurabh.singh@velsof.com](mailto:saurabh.singh@velsof.com)
- **Phone**: +91-8218804042
- **Address**: E-23, Sector-63, Noida
  