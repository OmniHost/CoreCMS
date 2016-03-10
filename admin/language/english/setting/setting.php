<?php

// Heading
$_['heading_title'] = 'Settings';

// Text

$_['text_success'] = 'Success: You have modified settings!';
$_['text_edit'] = 'Edit Setting';
$_['text_review'] = 'Comments';
//
$_['tab_general'] = 'General';
$_['tab_mail'] = 'Email';
$_['tab_server'] = 'Server';
$_['tab_seo'] = 'SEO';
$_['tab_google'] = 'Google';

$_['entry_name'] = 'Site Name';
$_['entry_email'] = 'Site Email';
$_['entry_meta_title'] = 'Meta Title';
$_['entry_meta_description'] = 'Meta Tag Description';
$_['entry_meta_keyword'] = 'Meta Tag Keywords';
$_['entry_layout'] = 'Default Layout';
$_['entry_template'] = 'Template';
$_['entry_logo'] = 'Site Logo';
$_['entry_icon'] = 'Icon';
$_['entry_mail_protocol'] = 'Mail Protocol';
$_['entry_mail_parameter'] = 'Mail Parameters';
$_['entry_mail_smtp_hostname'] = 'SMTP Hostname';
$_['entry_mail_smtp_username'] = 'SMTP Username';
$_['entry_mail_smtp_password'] = 'SMTP Password';
$_['entry_mail_smtp_port'] = 'SMTP Port';
$_['entry_mail_smtp_timeout'] = 'SMTP Timeout';
$_['entry_mail_alert'] = 'Additional Alert E-Mails';
$_['entry_secure'] = 'Use SSL';
$_['text_mail'] = 'Mail';
$_['text_smtp'] = 'SMTP';
$_['text_sendgrid'] = 'SendGrid';
$_['entry_sendgrid_key'] = 'Sendgrid API Key';
$_['text_google_analytics'] = 'Google Analytics';
$_['text_google_captcha'] = 'Google reCAPTCHA';
$_['text_google_api'] = 'Google API\'s';

$_['entry_encryption'] = 'Encryption Key';

$_['entry_maintenance'] = 'Maintenance Mode';

$_['entry_compression'] = 'Output Compression Level';
$_['entry_error_display'] = 'Display Errors';
$_['entry_error_log'] = 'Log Errors';
$_['entry_google_analytics'] = 'Google Analytics Code';
$_['entry_google_api'] = 'Google Public API access';
$_['entry_google_captcha_public'] = 'Site key';
$_['entry_google_captcha_secret'] = 'Secret key';
$_['entry_status'] = 'Status';
$_['entry_file_max_size'] = 'Max File Size';
$_['entry_file_ext_allowed'] = 'Allowed File Extensions';
$_['entry_file_mime_allowed'] = 'Allowed File Mime Types';

$_['entry_image_thumb'] = 'Gallery Thumb Size';
$_['entry_image_popup'] = 'Gallery Popup Size';

$_['entry_limit_admin'] = 'Default Items Per Page (Admin)';
$_['error_warning'] = 'Warning: Please check the form carefully for errors!';
$_['error_permission'] = 'Warning: You do not have permission to modify settings!';
$_['error_name'] = 'Site Name must be between 3 and 32 characters!';
$_['error_email'] = 'E-Mail Address does not appear to be valid!';
$_['error_meta_title'] = 'Title must be between 3 and 32 characters!';
$_['error_limit'] = 'Limit required!';
$_['error_encryption'] = 'Encryption Key must be between 3 and 32 characters!';


$_['help_maintenance'] = 'Prevents customers from browsing your store. They will instead see a maintenance message. If logged in as admin, you will see the store as normal.';
$_['help_limit_admin'] = 'Determines how many admin items are shown per page (pages etc).';
$_['help_icon'] = 'The icon should be a PNG that is 16px x 16px.';
$_['help_sendgrid_key'] = 'Go to <a href="http://sendgrid.com">SendGrid</a> and get your sending api key.';
$_['help_mail_protocol'] = 'Only choose \'Mail\' unless your host has disabled the php mail function.';
$_['help_mail_parameter'] = 'When using \'Mail\', additional mail parameters can be added here (e.g. -f email@storeaddress.com).';
$_['help_mail_smtp_hostname'] = 'Add \'tls://\' prefix if security connection is required. (e.g. tls://smtp.gmail.com).';
$_['help_mail_alert'] = 'Any additional emails you want to receive the alert email, in addition to the main store email. (comma separated).';
$_['help_secure'] = 'To use SSL check with your host if a SSL certificate is installed and add the SSL URL to the catalog and admin config files.';
$_['help_compression'] = 'GZIP for more efficient transfer to requesting clients. Compression level must be between 0 - 9.';
$_['help_google_analytics'] = 'Login to your <a href="http://www.google.com/analytics/" target="_blank"><u>Google Analytics</u></a> account and after creating your website profile copy and paste the analytics code into this field.';
$_['help_google_captcha'] = 'Go to <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank"><u>Google reCAPTCHA page</u></a> and register your website.';
$_['help_encryption'] = 'Please provide a secret key that will be used to encrypt private information.';
$_['help_google_api'] = 'Login to your <a href="https://code.google.com/apis/console/" target="_blank"><u>Google API Console</u></a>  and after creating your website profile copy and paste the codes here.';
$_['help_file_max_size'] = 'The maximum image file size you can upload in Image Manager. Enter as byte.';
$_['help_file_ext_allowed'] = 'Add which file extensions are allowed to be uploaded. Use a new line for each value.';
$_['help_file_mime_allowed'] = 'Add which file mime types are allowed to be uploaded. Use a new line for each value.';

$_['error_ftp_hostname'] = 'FTP Host required!';
$_['error_ftp_port'] = 'FTP Port required!';
$_['error_ftp_username'] = 'FTP Username required!';
$_['error_ftp_password'] = 'FTP Password required!';
$_['help_ftp_root'] = 'The directory your CoreCMS installation is stored in. Normally \'public_html/\'.';
$_['entry_ftp_hostname'] = 'FTP Host';
$_['entry_ftp_port'] = 'FTP Port';
$_['entry_ftp_username'] = 'FTP Username';
$_['entry_ftp_password'] = 'FTP Password';
$_['entry_ftp_root'] = 'FTP Root';
$_['entry_ftp_status'] = 'Enable FTP';
$_['tab_ftp'] = 'FTP';
$_['tab_option'] = 'Options';
$_['entry_product_limit'] = 'Default Items Per Page (Frontend)';
$_['entry_blog_limit'] = 'Default Articles Per Page (Frontend)';
$_['entry_customer_group'] = 'User Group';
$_['entry_customer_group_display'] = 'User Groups';
$_['entry_account'] = 'Account Terms';
$_['help_product_limit'] = 'Determines how many items are shown per page (pages, categories, etc)';
$_['help_blog_limit'] = 'Determines how many articles are shown per page (articles, blogs, etc)';
$_['help_customer_group'] = 'Default user group.';
$_['help_customer_group_display'] = 'Display user groups that new users can select to use when signing up.';
$_['help_account'] = 'Forces people to agree to terms before an account can be created.';
$_['entry_country'] = 'Default Country';
$_['entry_review'] = 'Allow Coments';
$_['entry_review_guest'] = 'Allow Guest Comments';
$_['entry_review_mail'] = 'New Comment Alert Mail';
$_['entry_comment_auto_approve'] = 'Comments Auto Approve';
$_['help_review'] = 'Enable/Disable new comment entry and display of existing coments.';
$_['help_review_guest'] = 'Allow guests to post comments.';
$_['help_review_mail'] = 'Send an email to the site owner when a new comment is created.';
$_['help_comment_auto_approve'] = 'You do not have to approve new coments. The comments will display immediately.';

$_['text_account'] = 'Account';
$_['entry_customer_online'] = 'Users Online';
$_['entry_customer_group'] = 'User Group';
$_['entry_customer_group_display'] = 'User Groups';
$_['entry_account'] = 'Account Terms';
$_['entry_account_mail'] = 'New Account Alert Mail';
$_['help_customer_group'] = 'Default user group.';
$_['help_customer_group_display'] = 'Display user groups that new users can select to use.';
$_['help_account'] = 'Forces people to agree to terms before an account can be created.';
$_['help_account_mail'] = 'Send an email to the site owner when a new account is registered.';

$_['entry_login_attempts'] = 'Max Login Attempts';
$_['help_login_attempts'] = 'Maximum login attempts allowed before the account is locked for 1 hour. User accounts can be unlocked on the user pages.';
$_['entry_currency'] = 'Currency';
$_['help_currency'] = 'Change the default currency. Clear your browser cache to see the change and reset your existing cookie.';
$_['help_currency_auto'] = 'Set your store to automatically update currencies daily.';
$_['entry_currency_auto'] = 'Auto Update Currency';
$_['entry_robots'] = 'Robots.txt contents';

$_['text_autosave'] = 'CMS Autosave';
$_['entry_autosave'] = 'Enable Autosave';
$_['entry_autosave_time'] = 'Save every:';
$_['help_autosave_time'] = 'How many seconds between autosaves';
$_['help_autosave'] = 'Disable or enable the autosave functionality, this will only affect autosave not revisions.';
$_['entry_autosave_seconds'] = 'seconds (min 30, default 120)';

$_['entry_account_register'] = 'Allow User Registration';
$_['help_account_register'] = 'If set Yes, users can register from the front end of the site using the Create an Account link provided on the Login page.';

$_['help_meta_title'] = 'This will be default page title';
$_['text_facebook_defaults'] = 'Default Facebook Open Graph Tags';
$_['text_twitter_defaults'] = 'Default Twitter Tags';
$_['text_google_defaults'] = 'Default Google / Schema Tags';

$_['help_meta_publisher'] = 'Rel=publisher tag is defined as the tag that links a brand’s Google+ page snippet to it’s search snippet.';
$_['entry_meta_publisher'] = 'Rel=Publisher Tag';
$_['help_meta_author'] = 'el=author associates an individual’s Google+ page to a website';
$_['entry_meta_author'] = 'Rel=Author Tag';

$_['entry_facebook_sitename'] = 'Facebook og:site_name';
$_['entry_facebook_title'] = 'Facebook og:title';
$_['entry_facebook_description'] = 'Facebook og:description';
$_['entry_facebook_image'] = 'Facebook og:image (min 200X200)';
$_['entry_facebook_appid'] = 'Facebook AppId';
$_['entry_meta_ogimage_help'] = 'Visit <a href="https://developers.facebook.com/docs/sharing/best-practices#images" target="_blank">Facebook Guidlines</a> for Best Guidlines regarding images';


$_['entry_sso'] = 'SSO API User';
$_['help_sso'] = 'Which api credentials to use when using SSO (with system like Vanilla forums)';
