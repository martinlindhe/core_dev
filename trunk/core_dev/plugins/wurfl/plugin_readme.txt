Plugin for wurfl, see http://wurfl.sourceforge.net/ for more information

wurfl.xml is dated 2007.03.03 and was last updated 2007.05.28

The PHP scripts are based on wurfl_php_tools_21beta2.zip


**** IMPORTANT **** 
Before using wurfl plugin you need to generate a cache.
Edit update_cache.php and comment the die; command in the beginning of the file.
Then execute the script to generate a cache.
Don't forget to uncomment that die; command when you are done.

Run the script from commandline, or call it by url, something like:

http://localhots/core_dev/plugins/wurfl/update_cache.php
**** IMPORTANT **** 



update_cache.php is a utility to update the cache.
By default the script starts with a die; command, which you need to edit out to use the script.
This is a security precaution.

But you shouldnt need to run update_cache.php, since a generated cache comes with the plugin.
