=== Easy Job Portal ===
Contributors: rejuancse
Tags: job portal, career, job listing, job manager
Requires at least: 5.0
Tested up to: 5.7
Requires PHP: 5.4.0
Stable tag:  1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Powerful & robust plugin to create a Job Portal on your website in a Easy & elegant way.

== Description ==
Add an easy modern Job Portal to your website. Display job listings and allow employers to submit and manage jobs all from the front-end.

= Looking for an easy, user-friendly and robust Job Portal plugin? = 
Easy Job Portal is light weight plugin that adds a job Portal to your WordPress website. 
This plugin is extendible and easy to use. A customized job Portal is created to manage various job offers via Wordpress with the Easy Job Portal. You can add multiple job listings and can show them on any page by inserting [easyjobpost] shortcode. You can add multiple job features and customized application forms for every distinct job listing. You can also add notes to an application right from the dashPortal.


= Easy Job Portal Shortcodes =

* [submit_job_form] 
* [job_dashboard] 
* [jobs] 


= Plugin Features =

*  Add, categorize and manage all jobs using the granular WordPress User Interface.
*  Allow job listers to add job types in job listings.
*  Add job location to an individual job created.
*  Add category shortcode to any post to enlist job listing of that particular category.
*  Add job Location to any post by using specified shortcode.
*  Add Job Type to any post by using specified shortcode.
*  Add a combination of multiple shortcodes for a job listing.
*  Use the Anti-hotlinking option to enhance the security of your documents.
*  Upload documents in various extensions.	
*  View Applicants' list who applied for a particular job.
*  Set job listing, job features, application form, filters and email notifications for a job through global settings.
*  Compatible with WPML since SJB version 2.9.0 


== Configurations & Templating ==

= Follow the following steps for a fully functional Job Portal: =
1. After installation, go to "Job Portal" menu in the admin panel, and add a new job listing.
1. Add multiple job features and a fully customized application form right from the job listing editor.
1. To list all the job listings and start receiving applications, add [easyjobpost] shortcode in an existing page or add a new page and write shortcode anywhere in the page editor.
1. After someone fills an application form from the front-end, you will receive it right in the dashPortal.
1. You can add special notes to an application by opening its detail page.

= Job Portal Templating = 

The job Portal templating feature allows you to change the following file templates. We are providing two UI layouts named as Classical and Modern.

1. For modifying classical layout templates, please refer to v1 directory.
2. For modifying modern layout templates, please refer to v2 directory.



1. To change a template, please add "Easy Job Portal" folder in your activated theme's root directory.
2. Add above mentioned file from plugin Easy-job-Portal >templates folder keeping the same file directory structure and do whatever you want.

Enjoy your work with Easy Job Portal templating.

== Installation ==

1. Download plugin.
1. Upload `easy-job-portal.zip` to the `/wp-content/plugins/` directory to your web server.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add a standard WordPress page or post and use the [easyjobpost] shortcode in the editor to make it a Job Portal.

== Frequently Asked Questions ==

= How to show job listings on the front-end? = 
To list all the job listings and start receiving applications, add [easyjobpost] shortcode in an existing page or add a new page and write shortcode anywhere in the page editor.

= Job Page Expands Across Entire Page =
It's container class naming issue. We can't set all websites container classes because every website has its own CSS and naming conventions.

So, we are giving the facility to Job Portal's users for adding container class or Id under Settings> Appearance tab. Please add your website container class in "Job Portal Container Class:" under Job Portal> Settings> Appearance tab.

= Where can I assign global settings for same job posts? =  
You can assign global job listing settings to each job post through settings.

= How can I add company information for a job post? = 
Once you are in new job page, you can add company information in job data section.

= Can I upload a resume with different extensions? = 
Yes, you can upload a resume document with .pdf, .odt, .txt, .rtf, .doc, .docx extensions from the settings page.

= Can I show only 5 latest jobs on front-end with pagination? = 
Yes, you can show any number of posts on your website with pagination feature by using shortcode with "posts" attribute i.e. [jobpost posts="5"]

= Can I show job listings for particular "Category" using a shortcode? = 
Yes, you can use a shortcode on post page i.e. [jobpost category="category-slug"]

= Can I show job listings for particular "Type" using a shortcode? = 
Yes, you can use a shortcode on post page i.e. [jobpost type="type-slug"]

= Can I show job listings for particular "Location" using a shortcode? = 
Yes, you can use a shortcode on post page i.e. [jobpost location="location-slug"]

= Can I use combination for various shortcodes to display job listings? = 
Yes, you can use various combinations of shortcodes with spaces i.e. [jobpost location="location-slug" category="category-slug" type="type-slug"]

= How Can I view the Applicant list for a Job Post? = 
In your WordPress admin panel, go to "Job Portal" menu and "Applicants" section

= Where can I find more information about Easy Job Portal? =  
You can visit <a href="#">WPQXTHEME Website</a> or <a href="#">blog</a> page.

== Screenshots ==

1. Job Listing
2. Job Board
3. Job Applications
4. Job Post

= 1.0.0 [14/02/2022] =

* Initial version released

== Upgrade Notice ==

Nothing here