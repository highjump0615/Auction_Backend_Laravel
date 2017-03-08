Auction Backend API, Laravel
======

> Auction and bidding for redundant goods, backend for iOS app.

## Overview

### 1. Main Features
- User management  
Signup, Login, Profile, Setting, ...
- Item Management  
Upload, Search, Category, ...
- Bid Management  
Place bid, Get max bid, Give up, Delete, ...
 
### 2. Techniques 
#### 2.1 Laravel PHP Framework v5.2.45
- Create and maintain database using migration
- Based on api middleware, api_token and Auth are used for user authentication
- Implement Cron jobs using Task Schedular, take advantage of Laravel framework  
Customize artisan command
- Showing error information as JSON when exception arises  
Setting Accept as application/json in header makes effect.

## Need to Improve
- Add logic for push notification
- Integrate chat feature  
... ...