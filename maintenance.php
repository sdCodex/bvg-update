<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Under Maintenance</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Global Styles and Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9; /* Light gray background */
            color: #333;
            /* Full viewport height and center alignment */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .maintenance-container {
            max-width: 600px;
            padding: 50px 40px; /* Increased padding */
            background: #fff;
            border-radius: 16px; /* Smoother corners */
            /* Enhanced shadow for a lifted look */
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15); 
            border-top: 5px solid #ff9800; /* Orange top border for emphasis */
        }

        /* Icon Styling */
        .icon-wrapper {
            font-size: 60px;
            color: #ff9800; 
            margin-bottom: 25px;
            display: inline-block;
            /* Simple Animation for subtle movement */
            animation: spin 4s linear infinite; 
        }

        /* Unicode for Gear/Wrench */
        .icon-wrapper::before {
             content: '\2699'; 
        }

        /* Heading Styles */
        h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 25px;
            color: #7f8c8d;
        }

        /* Highlighted Time/Date */
        .return-time {
            font-weight: 700;
            color: #e67e22; 
            font-size: 1.2rem;
            display: block;
            margin-top: 10px;
        }

        /* Button Styling */
        .btn-updates {
            background: #ff9800;
            color: #fff;
            padding: 12px 25px;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(255, 152, 0, 0.4);
        }

        .btn-updates:hover {
            background: #e67e22;
            transform: translateY(-2px);
        }

        /* Keyframe for subtle spinning animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .maintenance-container {
                margin: 20px;
                padding: 40px 20px;
            }
            h1 {
                font-size: 1.8rem;
            }
            .btn-updates {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>

    <div class="maintenance-container">
        <div class="icon-wrapper"></div> 

        <h1>Under Scheduled Maintenance</h1>
        
        <p>
            We're currently performing some **important updates and optimization** to bring you a faster and better experience. We sincerely apologize for the inconvenience!
        </p>

        <p>
            **Estimated downtime:**
            <span class="return-time">Will be back by 3:00 PM IST.</span>
        </p>
        
        <a href="mailto:info@ourgurukul.org" class="btn-updates">
            Email Us for Urgent Queries
        </a>
    </div>

</body>
</html>