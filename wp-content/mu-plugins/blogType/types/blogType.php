<?php
/**
 * @name Interface blogType
 * @abstract Interface of blogType
 * @author Antoni Bertran (abertranb@uoc.edu)
 * @copyright 2010 Universitat Oberta de Catalunya
 * @license GPL
 * @version 1.0.0
 * Date December 2010
*/

interface blogType
{
	/**
	 * 
	 * Get the name of course, if you want you can overwrite this method in your new plugin to customize the course name.
	 * @param LTI Object $blti
	 */
	public function getCourseName($blti);
	/**
	 * 
	 * Gets the path of blog (URL to site)
	 * @param LTI Object $blti
	 * @param unknown_type $siteUrlArray
	 * @param unknown_type $domain
	 */
	public function getCoursePath($blti, $siteUrlArray, $domain);
	/**
	 * 
	 * Set the Language to the blog, as default is using the launch_presentation_locale
	 * @param LTI Object $blti
	 */
	public function setLanguage($blti);
	/**
	 * 
	 * Change the theme to the blog
	 */
    public function changeTheme();
    /**
     * 
     * Indicate the list of plugins to load
     */
    public function loadPlugins();
    /**
     * 
     * Returns the role from LTI to Wordpress
     * @param String $role
     * @param LTI Object $blti
	 */
    public function roleMapping($role, $blti);
    /**
     * This function contains the last actions before show blog
     */
    public function postActions($obj);
}
?>