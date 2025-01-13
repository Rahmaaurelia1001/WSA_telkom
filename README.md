# Project: TTR Report Management System for MGR PERF, Service Quality, and Helpdesk Ops

## Project Description
This website is designed to assist the **MGR PERF**, **Service Quality**, and **Helpdesk Ops** divisions in managing **TTR (Time to Resolution)** reports for the **Whole Sales Agreement (WSA)** product. The system enables these teams to report and track disruptions and their resolution time, updated on an hourly basis.

The primary function of this website is to streamline data processing and monitor key performance indicators (KPIs) related to disruption resolution times. The site allows for data uploading, calculation of KPIs using internal formulas, and real-time tracking of performance metrics, ensuring that the defined TTR targets for their products are consistently met.

## Key Features
1. **Login System**  
   A secure login system ensures that only authorized users can access the website. Users log in with credentials provided by the administrator to manage their data.

2. **Data Upload**  
   Users can upload Excel files containing disruption data. The system will then merge data from different sources and prepare it for processing.

3. **Column-based Data Cleanup**  
   The platform allows users to delete data based on specific columns. This feature helps clean and refine the data before performing any calculations or analysis.

4. **Calculation with Internal Formulas**  
   The data is processed using predefined formulas based on internal documentation from the division. These formulas are used to calculate various KPIs such as **3-Hour Manja TTR**, **36-Hour WSA Non-HVC TTR**, **3-Hour Diamond WSA TTR**, and others.

5. **Dashboard KPI Monitoring**  
   The main dashboard displays the calculated data in an easily understandable format, allowing teams to monitor key KPIs in real-time. Key KPIs displayed include:
   - **3-Hour Manja KPI**: Displays the total time taken to resolve disruptions within 3 hours. The target for this KPI is set according to division standards.
   - **WSA 36-Hour Non-HVC Monitoring**: Monitors the number of disruptions resolved within 36 hours, with a target of **99.04%**.
   - **WSA 3-Hour Diamond TTR Monitoring**: Tracks the KPI for disruptions in the Diamond product, with a target of **82.24%**.
   - **WSA 6-Hour Platinum TTR Monitoring**: Tracks the KPI for disruptions in the Platinum product, with a target of **90.58%**.

## Website Purpose
The purpose of this website is to expedite the process of reporting and tracking disruptions for the WSA product, as well as to provide clear insights into KPI performance. By automating data processing and KPI calculation, the tool enables faster corrective actions, ensuring that service quality targets are achieved and maintained.

## How It Works
1. **Login**: Registered users log in to access the system.
2. **Upload Data**: After logging in, users upload an Excel file containing disruption reports.
3. **Data Cleanup**: Users can clean the uploaded data by deleting values in specific columns before further processing.
4. **Data Calculation**: Clean data is processed using the predefined formulas to calculate the required KPIs.
5. **KPI Monitoring Dashboard**: The processed data is displayed on the dashboard, allowing real-time monitoring of the performance metrics.

## Technologies Used
- **Laravel**: PHP framework used to build the website, enabling secure login, data processing, and backend operations.
- **Excel File Processing**: Supports Excel file uploads, allowing users to easily import and process data.
- **PHP & MySQL**: Used to manage data efficiently, storing and retrieving the information required for calculations.
- **Bootstrap**: For responsive design, ensuring the dashboard is accessible and user-friendly across devices.

## Benefits of This Website
- **Efficiency**: Reduces the time required for data processing and reporting.
- **Accuracy**: The automatic calculation of KPIs using internal formulas ensures that data is accurate and does not require manual verification.
- **Real-Time Monitoring**: Allows the team to monitor KPIs in real-time and take immediate corrective actions to ensure target achievement.


