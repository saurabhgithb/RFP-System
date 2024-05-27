# RFP Management System v1.0

## Overview
The RFP Management System is a web-based application developed by Velocity Software Solutions Pvt. Ltd. It provides a platform for government agencies and private companies to issue requests for proposals (RFPs) and allows vendors to submit quotes in response to these RFPs. The system helps streamline the RFP process, ensuring full and open competition while facilitating communication between companies and vendors.

## Features
- User authentication: Allows users to log in with their email and password or google.
- Admin panel: Provides administrators with the ability to issue RFPs, manage categories, approve vendors, and view quotes submitted by vendors.
- Vendor panel: Allows registered vendors to view open RFPs, submit quotes, and view their submitted quotes and edit their details.
- Registration process: Includes separate registration flows for admins and vendors, with email confirmation for vendor registration.
- Forgot password functionality: Allows users to reset their password via email.

# README

## RFP Management System

### Table of Contents

- [RFP Management System v1.0](#rfp-management-system-v10)
  - [Overview](#overview)
  - [Features](#features)
- [README](#readme)
  - [RFP Management System](#rfp-management-system)
    - [Table of Contents](#table-of-contents)
    - [Overview](#overview-1)
    - [Features](#features-1)
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
    git clone https://github.com/your-repository/rfp-management-system.git
    cd rfp-management-system
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

### Contact

For any issues or inquiries, please contact Velocity Software Solutions Pvt. Ltd.

- **Email**: [saurabh.singh@velsof.com](mailto:saurabh.singh@velsof.com)
- **Phone**: +91-8218804042
- **Address**: E-23, Sector-63, Noida
  