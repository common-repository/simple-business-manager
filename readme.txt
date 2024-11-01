=== Simple Business Manager ===

Contributors: russell.albin
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LVN2Z9HGWTBYA
Tags: business management, tools, management
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: trunk

Manage your customers, send invoices, track spending.

== Description ==

Simple Business Manager allows you to manage your company, track invoices and keep finances in order. Letter generator allows you to create letter templates and then send them to your customers. A Company report allows you to quickly see your company finances, expenses and deposits as well as the ability to track miles traveled.

== Installation ==

1. Unzip the plugin file.
2. Upload the "simple-business-manager" folder to the "/wp-content/plugins/" directory. The "simple-business-manager" file must be located at this address: "http://your-wordpress-directory-address/wp-content/plugins/simple-business-manager/".
3. Activate the plugin through the "Plugins" menu in WordPress.
4. Configure the plugin through the Simple Business Manager Settings page in WordPress.


== Frequently Asked Questions ==

= How and why should I use this =

This is the best one out in wordpress...A Real FAQ is coming soon

= What about products? =

This currently does not support individual products, but you can enter them via line item in each invoice

== Screenshots ==

/images/view_home_page.jpg

== Changelog ==
= 4.6.7.4 =
* 2/13/2013
* Fixing an sql error when counting the number of letters for a customer

= 4.6.7.3 =
* Adding ability to import current subscribers and make them customers
* Added some jquery to select all subscribers to turn them into customers

= 4.6.7.2 =
* Added ability to sort deposits and expenses by year
* Added ability to download deposits and expenses to a csv

= 4.6.7.1 =
* Fixed the total number of paid invoices on the Company Report, when you change years

= 4.6.7.0 =
* Fixed the Company Report, it is now showing the proper information if you change years.

= 4.6.6.9 =
* Fixed a mistake where I was calling a function instead of the object for accept payments

= 4.6.6.8 =
* Adjusted ability to cancel an invoice, you now have to view the invoiced invoice in order to see the Cancel button

= 4.6.6.7 =
* Had to adjust the formula that gets the balance due one more time to eliminate proposals as well as cancelled invoices

= 4.6.6.6 =
* The customer balance was originally based on any invoice that was not cancelled.  Changed to only invoices that have a status of invoiced.

= 4.6.6.5 =
* Short Tag bug fix.  For those of you who had issues with installing this plugin, Your server did not have short tags enabled.
* Renamed a static constant that may conflict with another plugin due to a very generic name I chose when starting this plugin
* Removed adding a second page to invoices due to a bug in the jquery clone that will not change the id of the cloned page

= 4.6.6.4 =
* I am done trying to upgrade TCPDF for now! Reverting to version just prior to this one.

4.6.6.3 =
* Apparently I just dont learn my lesson about upgrading TCPDF

= 4.6.6.2 =
* Fixed the bug that was showing the wrong message when the invoice was paid in full.

= 4.6.6.1 =
* This upgrade was probably the worst idea i have ever had.  Upgrading TCPDF turned out to break so, I reverted back to an old version
* I also had to revert all the code I wrote over the past 3 days..so I have that going for me as well.
* Anyway, if you upgraded anytime over the past 3 days, I am truly sorry!

= 4.6.6.0 =
* Upgrade TCPDF equals not good!
= 4.6.5.9 =
* Upgrade TCPDF equals not good!
= 4.6.5.8 =
* Upgrade TCPDF equals not good!
= 4.6.5.7 =
* Upgrade TCPDF equals not good!
= 4.6.5.6 =
* Upgrade TCPDF equals not good!
= 4.6.5.5 =
* Upgrade TCPDF equals not good!
= 4.6.5.4 =
* Upgrade TCPDF equals not good!
= 4.6.5.3 =
* Upgrade TCPDF equals not good!
= 4.6.5.2 =
* Upgrade TCPDF equals not good!

= 4.6.5.1 =
* moving the database upgrade check to the main page!  This way no matter what page you load it will see if the database upgrade script needs to be run

= 4.6.5.0 =
* Fixed some of the sort order options, to have the arrow display inline with the text
* When viewing the odometer, the sort links were broken and the year options may not work if you only had a few entries

= 4.6.4.9 =
* Removed a link on the menu that was not supposed to be there.
* Fixed the payee/payer name when viewing the entire list of odometer entries
* Added an animated gif to display when downloading the PDF after creating them.

= 4.6.4.8 =
* changed where the payee_payer_id is stored for odometer entries, moved it to the main table
* Fixed lots of payee_payer issues

= 4.6.4.7 =
* Added a link to quickly go to the company when viewing any type of invoice
* Added some text when canceling an invoice to remind the user that they can click the checkbox again if they would rather delete it

= 4.6.4.6 =
* Fixed an issue with the way the total amount due for a customer was determined

= 4.6.4.5 =
* Fixed the wording when you are either going to cancel, OR delete an invoice.
* Added some jquery to hide and change the text for cancel/delete invoices

= 4.6.4.4 =
* Added ability to change status of an invoice to Cancelled
* Fixed several bugs that would not allow you to view a paid invoice when viewing the customer account
* Also fixed a bug that was preventing cancelled invoices from being reactivated
* Applying a fix to the custom attributes for customers.  They were not adding correctly and you could not remove them if needed.

= 4.6.4.3 =
* The customer balance was not working, AT ALL, and I just figured that out.
* Fixed the code to show the customer balance as any payments minus all outstanding invoices that are not the status of cancelled

= 4.6.4.2 =
* Added donate link to the description as well as updated my donate link to paypal since the old one was broken.
* Reversed the ascending and descending arrows when sorting a list, I think I had them backwards

= 4.6.4.1 =
* Adding a screen shot of the home page to help potential users

= 4.6.4.0 =
*  Added the ability to tie a payee/payer to an odometer entry

= 4.6.3.9 =
*  Added a PO section for invoices

= 4.6.3.8 =
*  Fixed some options that were left when un-installing/deleting all data
*  The setup for a new install was broken, and should now be fixed.  This happened when adding a column to the database in 2 tables.

= 4.6.3.7 =
*  Adjusted the company report and tweaked the layout and text for the company report page
*  Put the total miles on the company report

= 4.6.3.6 =
*  Bug with entering new/editing odometer data fixed it was not saving the date properly
*  When asking to view all odometer listings, it was not working properly
*  Sorting by date was not working properly
*  When changing the sort by columns name, the year was not be kept and was resetting to all

= 4.6.3.5 =
*  When viewing the company report, the total number of paid invoices was showing up wrong
*  Put total miles on company report, as well as ability to export odometer report as csv
*  Adjusted the odometer table to keep track of the date to sort better
*  Upgrade database version to 2.4

= 4.6.3.4 =
*  Fixed a bug that was allowing for multiple customers to have login accounts
*  Fixed the output for customers when they log in, they will only get their invoice lists, not the entire list
*  Removed the buttons to create invoice and edit their account...even though they did not work anyway
*  Added some error protection and validation when editing a customer account to prevent multiple accounts

= 4.6.3.3 =
*  Bug fixed where you could not delete an invoice.  It was a javascript error causing the bug.

= 4.6.3.2 =
*  You can now enter a negative value in invoices to show a credit if needed.
*  Commented out and not displaying the pages/data for projects....its still not working right
*  I needed to commit this update now because I have a credit I need to apply to a customer.
*  Fixed the upgrade process, there was a glitch that was not upgrading from version to version quite right

= 4.6.3.1 =
*  New Feature Project Management
*  Updated the readme.txt to show the new table structure for projects, for anyone who may want to dig into the code
*  Upgraded database version to 2.3

= 4.6.3.0 =
*  Changed the plugin url to show the new location on my site www.russellalbin.com

= 4.6.2.9 =
*  Fixed a bug that was allowing a row to be cleared when viewing a paid or invoiced item.
*  Although it appeared to only be cosmetic, i removed that option when viewing those types of invoices

= 4.6.2.8 =
*  Added ACH as a payment type
*  Changed the output when creating a PDF for download, the name now says invoice instead of invoiced

= 4.6.2.7 =
*  Found a bug that would output the company name to the next record when viewing the list of invoices
*  if the next customer did not have a company name

= 4.6.2.6 =
*  Removed some debugging output
*  fixed some formatting issues

= 4.6.2.5 =
*  Fixed a bug that was not fading out the messages
*  Fixed some javascript that was always putting an amount when creating an invoice, even though it is just a comment
*  Added a clear line feature to invoices so your an clear out all the data in the one line
*  Changed the message when you create/edit an invoice.

= 4.6.2.4 =
*  7/7/2011
*  Created the ability to do a reversal for an expense or deposit

= 4.6.2.3 =
*  7/7/2011
*  Entering Deposits and Expenses had a flaw with the dynamic addition of a payee/payer and was not saving that information
*  Fixed the grey box that appears when entering or selecting a payee/payer that would not disappear when you clicked close, or tabbed out of the last field

= 4.6.2.2 =
*  6/26/2011
*  Removed some content I had in place when testing when on View/Edit customers
*  Removed some content showing when I was testing Suggested Destination when adding/editing an Odometer entry.

= 4.6.2.1 =
*  6/26/2011
*  Drastically changed the SQL to get the list of destinations!  My original version was HIGHLY flawed and returned many irregular results!  This is much better and FASTER!

= 4.6.2.0 =
*  6/26/2011
*  Fixed an issue that was not displaying the suggested destination properly when entering the Odometer information.
*  When deleting an odometer entry, the values were not being completely removed.  Now there is a check to remove those abandoned entries
*  Added a description to the delete odometer page.  This way you can double check what you are deleting.

= 4.6.1.9 =
*  6/25/2011
*  Modified the css to show the suggestions for the Odometer as underlined and the cursor is a pointer to assist users with visual cues that the results are for links to auto fill in the destination.

= 4.6.1.8 =
*  6/25/2011
*  Added the a message if no results are found when searching for suggestions in the Odometer create/edit

= 4.6.1.7 =
*  6/24/2011
*  Added the arrows to the odometer to show how the page is organized and the results are being displayed

= 4.6.1.6 =
*  6/24/2011
*  Odometer now has a suggestion box. It pulls in all old entries that match the start of the spelling for the word your typing. Max 10 results shown.

= 4.6.1.5 =
*  6/24/2011
*  Odometer now has a scrolling window when it gets rather long.  Also the total miles has moved to above the entry field.

= 4.6.1.4 =
*  6/24/2011
*  Odometer now has some basic validation when entering miles.  If you use the same date, desitination and miles it will ask if this is a duplicate and allow you to change your values or over-ride it and allow for a duplicate entry

= 4.6.1.3 =
*  6/24/2011
*  Changed some wording on the settings page.  Apparently I was not checking my grammar when I wrote some of that page.
*  Changed the default action on view home page, now it shows all invoices, not just those that have a status of invoiced

= 4.6.1.2 =
*  6/24/2011
*  Small adjustment to the css for better positioning of the arrows

= 4.6.1.1 =
*  6/24/2011
*  Added some arrows to show the current sorting option when viewing the customer list

= 4.6.1.0 =
*  6/13/2011
*  Updated the edit customer to not show notes as a custom attribute as well as updated the validation for the email address

= 4.6.0.9 =
*  6/9/2011
*  Notes for customers were showing up in the custom attributes section

= 4.6.0.8 =
*  6/7/2011
*  Bug found and fixed! Issue found that the customers were not allowed to view the home page due to improper permissions.

= 4.6.0.7 =
*  6/5/2011
*  When changing or entering a customer name on invoices, there are times where the names are duplicated.  Instead of just returning the results, we are now building the array of results, then removing duplicates, to prevent this from happening.

= 4.6.0.6 =
*  6/5/2011
*  Trying to address the problem with multiple instances of jQuery being called, this release changed the way we are using the jquery library, and some UI features.  I always knew that enque_script was the proper way to include files, but neglected to read up on why.  I modified this latest version to use that recommended way to include javascript files.
*  This version also removed several files and combined several files to make the overall file size a bit smaller.
*  This should help install time, as we dropped 2MB in file size with this release
*  Changed how the function is called when an old invoice is loaded and it is trying to populate the old values
*  Changed how the validation happens when selecting customers to receive a letter

= 4.6.0.5 =
*  6/4/2011
*  Customers can now be give a password to allow them access to their account information
*  The password is show in the view/edit customer page as well as View Customer Account page
*  Changed some copy on the view customer account
*  The page View Customer Account now shows the Custom Attributes you can add when you edit/create a customer

= 4.6.0.4 =
*  6/2/2011
*  Updated the description
*  Fixed a bug that was not showing the word Invoice when viewing the unpaid invoices.  This is the word next to the invoice number at the top left
*  The word description was not centered on the invoice page

= 4.6.0.3 =
*  5/29/2011
*  Removed my contact information from the home page to the help page

= 4.6.0.2 =
*  5/29/2011
*  Removed some in-line css from the edit customers page

= 4.6.0.1 =
*  5/29/2011
*  Custom attributes can now be added to customers information

= 4.6.0.0 =
*  5/29/2011
*  Changing plugin status to Stable Release
*  Found a few more links that had a bad function name. Renamed them to the correct function name.

= 4.5.9 =
*  5/29/2011
*  The jQuery event listener was wrong, it needed to be .change not .click.  The event was failing in Chrome.

= 4.5.8 =
*  5/29/2011
*  Enhanced the help slightly.  Now there is a page that shows all current help topics including the new public customer sign up

= 4.5.7 =
*  5/29/2011
*  Short Codes implemented, Public Customer creating is now possible with the shortcode [public_customer_signup]

= 4.5.6 =
*  5/27/2011
*  Changed some of the navigation to single word instead of multiple words

= 4.5.5 =
*  5/27/2011
*  Fixing the navigation, some of the newly adjusted menu options where showing errors when the page loads

= 4.5.4 =
*  5/27/2011
*  There was a bad link on the company profile.

= 4.5.3 =
*  5/27/2011
*  The admin navigation was updated. Now using the Wordpress Way for icons.  Removed my version of css that was messing up other plugins' icons
*  Fixed tax rate on invoices, it was not showing the proper tax rate, although the tax rate was correct when the invoice was being calculated.

= 4.5.2 =
*  5/25/2011
*  Changed it so when you have already changed the status to Invoiced then you can no longer edit the line items

= 4.5.1 =
*  5/25/2011
*  Forgot to include the total tax paid for the new download PDF of invoice

= 4.5.0 =
*  5/25/2011
*  Created the ability to download a pdf of the invoices.  Although using the browser save as pdf seems to be a better option

= 4.4.9 =
*  5/24/2011
*  Found 2 more places that the currency setting was using the default instead of the set value determined by the settings.  Thanks again Mara for finding this bug!

= 4.4.8 =
*  5/23/2011
*  When viewing a customers account, the total number of paid invoices how appears as well as a total amount for that customer

= 4.4.7 =
*  5/23/2011
*  The link to down load your newly created PDF will use a new window instead of the same one to avoid needing the use of the back button on some browsers.  ( Thanks for the tip Mara! ).

= 4.4.6 =
*  5/21/2011
*  Changed the text on invoices to show the words proposal and canceled, etc depending on the status of the invoice

= 4.4.5 =
*  5/21/2011
*  Changed Default currency to be an input box and the user can enter whatever symbol/html character needed
*  The PDF Letter generator now uses the company logo providing one has been entered in the settings. Max Width: 570px Height: 89px
*  The default time zone is now an input box and the customer is responsible for entering correct information

= 4.4.4 =
*  5/17/2011
*  The Settings link was being placed on every plugin activated and has been fixed

= 4.4.3 =
*  5/17/2011
*  Added South African Rand as currency option ZAR displayed as R

= 4.4.2 =
*  5/16/2011
*  Added some scroll area to some pages that the content will get really long after time

= 4.4.1 =
*  5/16/2011
*  Found out that my javascript is NOT going to work with Internet Explorer 7!
*  Bug Fix, fixed another area that was looking for the PDF in the wrong folder.

= 4.4.0 =
*  5/16/2011
*  Bug Fix in the PDF creation.  The folder was not being created with the proper name.  Also, regarding the letter generator, I just found out that if you are using IE7, the buttons do not put the proper text in the letter...(Still need to fix)

= 4.3.9 =
*  5/12/2011
*  Added ability to view this readme.txt file from the plugin

= 4.3.8 =
*  5/12/2011
*  Added some validation to prevent duplicate expense type names and deposit type names

= 4.3.7 =
*  5/12/2011
*  Changed invoice page to disable inputs when viewing a paid invoice, or cancelled

= 4.3.6 =
*  5/12/2011
*  Changed invoice page to show Proposal when new or not converted to invoice, Invoice will show when it has been changed
*  Fixed a bug that was adding an extra space when trying to enter a new customer in the search box, when editing an invoice
*  Provided new links to view paid, invoiced and cancelled invoices.

= 4.3.5 =
*  5/6/2011
*  Changed logic to only include my css files on sbm pages
*  Created a new css file to hold the icon images and removed it from the main css file

= 4.3.4 =
*  5/6/2011
*  Changed logic to only include my jquery files on sbm pages

= 4.3.3 =
*  5/6/2011
*  Removed some extra CSS and renamed several jQuery functions to help with plugin fighting issues

= 4.3.2 =
*  5/6/2011
*  Css added to allow for vertical align to the top for certain elements
*  Upgrade notices created for version 4.2.7, 4.2.8, 4.2.9 and 4.3.0 for potential missing javascript files for new installs
*  Added a CSS reset to make sure things display as expected
*  Bug Fix There was an error when createing a new invoice, the new customer information was not showing up properly
*  Bug Fix Changed all javascript functions to have sbm_ prefix to avoid name collision with other plugins
*  Bug Fix Starting the process to allow each customer to have their own currency, but only started at this time, the converter will be the hardest part

= 4.3.1 =
*  5/4/2011
*  Fixed the readme.txt to be properly formated

= 4.3.0 =
* 5/4/2011
* Bug Fix Added some text to the description for this plugin and adjusted the License GPLv2 or later

= 4.2.9 =
* 5/4/2011
*  Needed a css style for the Settings page to show off the area where you can remove all the tables and data if needed.

= 4.2.8 =
* 5/4/2011
*  Trying to get rid of some installation errors, one being a "Plugin does not have a standard header"

= 4.2.7 =
* 5/3/2011
*  Added Yen as a currency option
*  Added quick links to add payee / payer and Odometer
*  There was no padding for the customer information on invoices
*  Updated the CSS to not affect the 4 column layout

= 4.2.6 =
* 5/3/2011
*  Invoiced items can now be reviewed to print again etc  However you can not edit an invoiced, paid or cancelled invoice

= 4.2.5 =
* 5/3/2011
*  Invoice number now appears on the top of the invoice above the customer information

= 4.2.4 =
* 5/3/2011
*  Added ability delete a pending invoice

= 4.2.3 =
* 5/2/2011
*  Added ability to have either a short menu or full menu  Thanks for the tip Steve from http://www.forestpcrepairs.com/

= 4.2.2 =
* 5/2/2011
* Moved changelog to this readme.txt
* Obviously, created this readme.txt file

= 4.2.1 =
* 5/1/2011
*  When using the link to create a new invoice, when viewing all customers and they do not have a company name, it was not putting the first and last name in the select customer box

= 4.2.0 =
* 5/1/2011
*  Fixing invoice if you dont have a company name for the customer

= 4.1.9 =
* 5/1/2011
*  Added a button to create an invoice for a customer when viewing that customers account

= 4.1.8 =
* 4/29/2011
*  Added some Help notes for the image for invoice url and the options to left, center or right justify that image
*  On Settings page, Address 2 is not required, removed the icon that shows that it would be

= 4.1.7 =
* 4/29/2011
*  Moved the company information around to be more consistant with typical invoice formats
*  Added a second address line to the Company settings
*  Added image option to top of invoices
*  Added Feature to use an image in the gallery as the image at the top of an invoice
*  Fixed the way the invoice page uses either the customers hourly rate or the default
*  Removed console.log from the javascript pages, they will cause issues to certain browsers
*  Removed the print option on the invoices, wordpress does not play well with that option

= 4.1.6 =
* 4/29/2011
*  When creating a second page, with an hourly invoice, the quantity was not be removed on the second page
*  Address not transferring over with new customer selection when editing an existing invoice
*  Paid Down was not showing 0.00 if nothing was paid down
*  The Letter Generator was not working at all, its fixed now

= 4.1.5 =
* 4/29/2011
*  To help diagnose table structure issues, a new section to Settings was added to output the current table structure

= 4.1.4 =
* 4/29/2011
*  Invoices now put the company name and the customer information on the top of the page
*  The customer search for invoices was not searching first or last names, it was searching company names only

= 4.1.3 =
* 4/28/2011
*  updated the "State" in the settings to be State/Province and increased its size to 8 with a max of 50 characters

= 4.1.2 =
* 4/28/2011
*  Updated the css for printing to remove the buttons and the type of invoice, as well as the upgrade nag
*  Wrong logic figuring out line totals on invoices, it was not totaling correct if you had unpaid time

= 4.1.1 =
* 4/27/2011
*  Added some different currency options such as euro and pound
*  Dropped the required Company Name for customers>
*  Originally I had the admin menu larger but it does distort other plugins, this was removed

= 4.1.0 =
* 4/27/2011
*  Added a link to side menu to connect to the settings, instead of only being able to click on the SBM Settings  main link
*  New admin menu in wordpress now has our Invoice menu options

= 4.0.9 =
* 4/27/2011
*  Removed an extra Dollar sign found when creating and editing invoices
*  There was a flaw in the logic when getting the updated line totals for new or editing old hourly invoices

= 4.0.8 =
* 4/26/2011
*  When creating an hourly invoice, if the unpaid time was 60 minutes or more, it was not calculating properly the final hours worked
*  removed an old debug message on the create invoice page
*  added some padding to the top menu option if the user has a few plugins installed and also the last menu option

= 4.0.7 =
* 4/25/2011
*  Cancel Button on Enter Odometer was not working properly
*  On an upgrade the current version number was not updating on the settings page. Now using the data found in the wordpress options instead

= 4.0.6 =
* 4/25/2011
*  Moved the link to create a new odometer on the left menu to the top of the Odometer category
*  Moved the link to create a new payee/payer on the left menu to the top of the Payee/Payer category
*  Moved the link to create a new invoice on the left menu to the top of the Invoices category
*  Moved the link to create a new customer on the left menu to the top of the Customer category
*  Now showing all the invoices on the home page
*  Change the welcome text on the home page to advise of the release candidate version
*  Expense types should now show up in alphabetical order AtoZ
*  Deposit types should now show up in alphabetical order AtoZ
*  Transaction types should now show up in alphabetical order AtoZ

= 4.0.5 =
* 4/25/2011
* Upgraded to Release Candidate
*  Added another button to quickly add a customer when viewing the list
*  Made the email address input a bit larger and shrunk the hourly rate box when editing a customer
*  When viewing the customer list, you can now sort by company name

= 4.0.4 =
*  4/25/2011
*  Added a button to quickly add to the Odometer when viewing the list
*  Added a button to quickly add a customer when viewing the list
*  Added a button to quickly add an invoice when viewing the list
*  Added customer information such as hourly rate and tax rate along with notes on the view customer page

= 4.0.3 =
*  4/25/2011
*  New css and background images for the naivation menu
*  Company report now has total invoices, and total dollar amount for invoices
*  View paid invoices
*  Customer Account updated and now is using real information
*  View pending invoices
*  Now showing list of customers with unpaid invoices
*  Company Report had a function that required 2 arguments, however had them backwards in a few cases
*  Error in logic when checking for payment of invoices was not taking into account any partial payments
*  Not recalling invoice status, paid down, hourly rate and tax rate when editing pending invoice
*  Not updating rows if change in invoice times or quantity unless doen in a certain order, how updates on any focus to any input

= 4.0.2 =
*  4/23/2011
*  Minor CSS change to show header and footer, instead of hiding them

= 4.0.1 =
*  4/23/2011
*  Database version 2.2 now being used. Added deposits table and Odometer
*  Company Report has been started
*  Odometer pages created to keep track of miles
*  Prevent a delete of used deposit and expense types fixed
*  View list of deposits and expenses
*  Added validation to new customers form
*  Added sort payee/payer feature when viewing the list
*  Enter Deposits and Expenses is complete
*  Added/improved the Payee/Payer section
*  Added a donate button to the settings page
*  A few Expense types are now created during installation
*  Updated the description of this plugin
*  View company report, including year to date income and expenses, etc.
*  Added this changelog and renamed main page for this plugin
*  Cleaned up the way deposit types and expense types are created, and removed some old functions related to those processes
*  a wicked bug that was not installing some default data properly on setup. Adjusted the upgrade database function to remedy that bug
*  Changed the css to show the changelog a bit more clean and organized
*  Moved the upgrade database function to after the setup for new installed customers to get some default data

= 4.0.0 =
*  4/22/2011
*  Significant update since Alpha 3.0.0  Major overhaul of database tables and cleaned up every line of code throughout the site

= 3.0.0 =
*  4/21/2011
* massive code clean up

= 1.0.0 =
*  4/10/2011
*  Basic development of site and functions, removed old functions and unsued code

== Upgrade Notice ==

= 4.2.7 =
Potential for missing javascript files for new installs UPDATE REQUIRED
= 4.2.8 =
Potential for missing javascript files for new installs UPDATE REQUIRED
= 4.2.9 =
Potential for missing javascript files for new installs UPDATE REQUIRED
= 4.3.0 =
Potential for missing javascript files for new installs UPDATE REQUIRED
= 4.6.3.1 =
New feature, Project Management UPDATE REQUIRED
= 4.6.3.5 =
Upgrade database to 2.4 handle new date field for odometer entries
= 4.6.6.5 =
Fixed the bug that was preventing some people from being able to use this plugin.  Removed any short tags used!

== Arbitrary section ==

A description of the table structure

Table: spm_project

        ID              = auto increment number
        date_submitted  = the date the project started format: YYYY-MM-DD HH:MM:SS
        last_updated    = the last date the project was updated format: YYYY-MM-DD HH:MM:SS

Table: spm_project_attribute

        ID  value

        1   name
        2   category
        3   status ( The actual value comes from spm_project_status_options )
        4   reporter
        5   assigned to
        6   priority
        7   summary
        8   description
        9   customer id
        10  change

Table: spm_project_details

        project_id      = The id from spm_project
        attribute_id    = The ID from spm_project_attribute such as: 1
        value           = The value such as: Fix the home page

Table: spm_project_status_options

        ID  Value

        1   new
        2   unassigned
        3   assigned
        4   acknowledged
        5   feedback
        6   confirm
        7   resolved
        8   closed

Please let me know if you have any questions.  russell@russellalbin.com

== A brief Markdown Example ==


