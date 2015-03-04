<?php

class Listify_Strings {

	public $strings;

	public function __construct() {
		$this->labels = array(
			'singular' => listify_theme_mod( 'label-singular' ),
			'plural' => listify_theme_mod( 'label-plural' )
		);

		$this->strings = $this->get_strings();

		add_filter( 'gettext', array( $this, 'gettext' ), 0, 3 );
		add_filter( 'gettext_with_context', array( $this, 'gettext_with_context' ), 0, 4 );
		add_filter( 'ngettext', array( $this, 'ngettext' ), 0, 5 );
	}

	public function label($form, $slug = false) {
		$label = $this->labels[ $form ];

		if ( '' == $label && 'plural' == $form ) {
			$label = 'Listings';
		} elseif ( '' == $label && 'singular' == $form ) {
			$label = 'Listing';
		}

		if ( ! $slug ) {
			return $label;
		}

		return sanitize_title( $label );
	}

	public function gettext( $translated, $original, $domain ) {
		if ( isset ( $this->strings[$domain][$original] ) ) {
			return $this->strings[$domain][$original];
		} else {
			return $translated;
		}
	}

	public function gettext_with_context( $translated, $original, $context, $domain ) {
		if ( isset ( $this->strings[$domain][$original] ) ) {
			return $this->strings[$domain][$original];
		} else {
			return $translated;
		}
	}

	public function ngettext( $original, $single, $plural, $number, $domain ) {
		if ( isset ( $this->strings[$domain][$original] ) ) {
			return $this->strings[$domain][$original];
		} else {
			return $original;
		}
	}

	private function get_strings() {
		$strings = array(
			'wp-job-manager' => array(
				'Job' => $this->label( 'singular' ),
				'Jobs' => $this->label( 'plural' ),
				'Job Listings' => $this->label( 'plural' ),
				'jobs' => $this->label( 'plural', true ),
				'job' => $this->label( 'singular', true ),

				'Job category' => sprintf( __( '%s Category', 'listify' ), $this->label( 'singular' ) ),
				'Job categories' => sprintf( __( '%s Categories', 'listify' ), $this->label( 'singular' ) ),
				'Job Categories' => sprintf( __( '%s Categories', 'listify' ), $this->label( 'singular' ) ),
				'job-category' => sprintf( __( '%s-category', 'listify' ), $this->label( 'singular', true ) ),

				'Job type' => sprintf( __( '%s Type', 'listify' ), $this->label( 'singular' ) ),
				'Job types' => sprintf( __( '%s Types', 'listify' ), $this->label( 'singular' ) ),
				'Job Types' => sprintf( __( '%s Types', 'listify' ), $this->label( 'singular' ) ),
				'job-type' => sprintf( __( '%s-type', 'listify' ), $this->label( 'singular', true ) ),

				'Jobs will be shown if within ANY selected category' => sprintf( __( '%s will be shown if within ANY
				selected category', 'listify' ), $this->label( 'plural' ) ),
				'Jobs will be shown if within ALL selected categories' => sprintf( __( '%s will be shown if within ALL
				selected categories', 'listify' ), $this->label( 'plural' ) ),

				'Application email' => __( 'Contact Email', 'listify' ),
				'Application URL' => __( 'Contact URL', 'listify' ),
				'Application email/URL' => __( 'Contact email/URL', 'listify' ),

				'Position filled?' => __( 'Listing filled?', 'listify' ),

				'A video about your company' => __( 'A video about your listing', 'listify' ),

				'Job Submission' => sprintf( '%s Submission', $this->label( 'singular' ) ),
				'Submit Job Form Page' => sprintf( 'Submit %s Form Page', $this->label( 'singular' ) ),
				'Job Dashboard Page' => sprintf( '%s Dashboard Page', $this->label( 'singular' ) ),
				'Job Listings Page' => sprintf( '%s Page', $this->label( 'plural' ) ),

				'Add a job via the back-end' => sprintf( __( 'Add a %s via the back-end', 'listify' ), $this->label(
				'singular', true ) ),
				'Add a job via the front-end' => sprintf( __( 'Add a %s via the front-end', 'listify' ), $this->label(
				'singular', true ) ),
				'Find out more about the front-end job submission form' => sprintf( __( 'Find out more about the
				front-end %s submission form', 'listify' ), $this->label( 'singular', true ) ),
				'View submitted job listings' => sprintf( __( 'View submitted %s listings', 'listify' ), $this->label(
				'singular' ) ),
				'Add the [jobs] shortcode to a page to list jobs' => sprintf( __( 'Add the [jobs] shortcode to a page to
				list %s', 'listify' ), $this->label( 'plural', true ) ),
				'View the job dashboard' => sprintf( __( 'View the %s dashboard', 'listify' ), $this->label( 'singular',
				true ) ),
				'Find out more about the front-end job dashboard' => sprintf( __( 'Find out more about the front-end %s
				dashboard', 'listify' ), $this->label( 'singular', true ) ),

				'Company name' => __( 'Company name', 'listify' ),
				'Company website' => __( 'Company website', 'listify' ),
				'Company tagline' => __( 'Company tagline', 'listify' ),
				'Brief description about the company' => __( 'Brief description about the company', 'listify' ),
				'Company Twitter' => __( 'Company Twitter', 'listify' ),
				'Company logo' => __( 'Company logo', 'listify' ),
				'URL to the company logo' => __( 'URL to the company logo', 'listify' ),
				'Company video' => __( 'Company video', 'listify' ),

				'WP Job Manager Add-ons' => __( 'WP Job Manager Add-ons', 'listify' ),
				'Settings' => __( 'Settings', 'listify' ),
				'Add-ons' => __( 'Add-ons', 'listify' ),
				'Approve %s' => __( 'Approve %s', 'listify' ),
				'Expire %s' => __( 'Expire %s', 'listify' ),
				'%s approved' => __( '%s approved', 'listify' ),
				'%s expired' => __( '%s expired', 'listify' ),
				'Select category' => __( 'Select category', 'listify' ),
				'Position' => __( 'Position', 'listify' ),
				'%s updated. View' => __( '%s updated. View', 'listify' ),
				'Custom field updated.' => __( 'Custom field updated.', 'listify' ),
				'Custom field deleted.' => __( 'Custom field deleted.', 'listify' ),
				'%s updated.' => __( '%s updated.', 'listify' ),
				'%s restored to revision from %s' => __( '%s restored to revision from %s', 'listify' ),
				'%s published. View' => __( '%s published. View', 'listify' ),
				'%s saved.' => __( '%s saved.', 'listify' ),
				'%s submitted. Preview' => __( '%s submitted. Preview', 'listify' ),
				'M j, Y @ G:i' => __( 'M j, Y @ G:i', 'listify' ),
				'%s draft updated. Preview' => __( '%s draft updated. Preview', 'listify' ),
				'Type' => __( 'Type', 'listify' ),
				'Posted' => __( 'Posted', 'listify' ),
				'Expires' => __( 'Expires', 'listify' ),
				'Categories' => __( 'Categories', 'listify' ),
				'Featured?' => __( 'Featured?', 'listify' ),
				'Filled?' => __( 'Filled?', 'listify' ),
				'Status' => __( 'Status', 'listify' ),
				'Actions' => __( 'Actions', 'listify' ),
				'ID: %d' => __( 'ID: %d', 'listify' ),
				'M j, Y' => __( 'M j, Y', 'listify' ),
				'by a guest' => __( 'by a guest', 'listify' ),
				'by %s' => __( 'by %s', 'listify' ),
				'Approve' => __( 'Approve', 'listify' ),
				'View' => __( 'View', 'listify' ),
				'Edit' => __( 'Edit', 'listify' ),
				'Delete' => __( 'Delete', 'listify' ),
				'Listings Per Page' => __( 'Listings Per Page', 'listify' ),
				'How many listings should be shown per page by default?' => __( 'How many listings should be shown per page by default?', 'listify' ),
				'Filled Positions' => __( 'Filled Positions', 'listify' ),
				'Hide filled positions' => __( 'Hide filled positions', 'listify' ),
				'If enabled, filled positions will be hidden.' => __( 'If enabled, filled positions will be hidden.', 'listify' ),
				'Enable categories for listings' => __( 'Enable categories for listings', 'listify' ),
				'Multi-select Categories' => __( 'Multi-select Categories', 'listify' ),
				'Enable category multiselect by default' => __( 'Enable category multiselect by default', 'listify' ),
				'Category Filter Type' => __( 'Category Filter Type', 'listify' ),
				'Account Required' => __( 'Account Required', 'listify' ),
				'Submitting listings requires an account' => __( 'Submitting listings requires an account', 'listify' ),
				'Account Creation' => __( 'Account Creation', 'listify' ),
				'Allow account creation' => __( 'Allow account creation', 'listify' ),
				'Account Role' => __( 'Account Role', 'listify' ),
				'Approval Required' => __( 'Approval Required', 'listify' ),
				'New submissions require admin approval' => __( 'New submissions require admin approval', 'listify' ),
				'If enabled, new submissions will be inactive, pending admin approval.' => __( 'If enabled, new submissions will be inactive, pending admin approval.', 'listify' ),
				'Allow Pending Edits' => __( 'Allow Pending Edits', 'listify' ),
				'Submissions awaiting approval can be edited' => __( 'Submissions awaiting approval can be edited', 'listify' ),
				'Listing Duration' => __( 'Listing Duration', 'listify' ),
				'Application Method' => __( 'Application Method', 'listify' ),
				'Choose the contact method for listings.' => __( 'Choose the contact method for listings.', 'listify' ),
				'Email address or website URL' => __( 'Email address or website URL', 'listify' ),
				'Email addresses only' => __( 'Email addresses only', 'listify' ),
				'Website URLs only' => __( 'Website URLs only', 'listify' ),
				'Pages' => __( 'Pages', 'listify' ),
				'Settings successfully saved' => __( 'Settings successfully saved', 'listify' ),
				'--no page--' => __( '--no page--', 'listify' ),
				'Select a page…' => __( 'Select a page…', 'listify' ),
				'Save Changes' => __( 'Save Changes', 'listify' ),
				'Setup' => __( 'Setup', 'listify' ),
				'Skip this step' => __( 'Skip this step', 'listify' ),
				'All Done!' => __( 'All Done!', 'listify' ),
				'Location' => __( 'Location', 'listify' ),
				"e.g. \'London\'" => __( "e.g. \'London\'", 'listify' ),
				'Leave this blank if the location is not important' => __( 'Leave this blank if the location is not important', 'listify' ),
				'Application email/URL' => __( 'Application email/URL', 'listify' ),
				'URL or email which applicants use to apply' => __( 'URL or email which applicants use to apply', 'listify' ),
				'URL to the company video' => __( 'URL to the company video', 'listify' ),
				'Position filled?' => __( 'Position filled?', 'listify' ),
				'Feature this listing?' => __( 'Feature this listing?', 'listify' ),
				'yyyy-mm-dd' => __( 'yyyy-mm-dd', 'listify' ),
				'Posted by' => __( 'Posted by', 'listify' ),
				'%s Data' => __( '%s Data', 'listify' ),
				'Use file' => __( 'Use file', 'listify' ),
				'Upload' => __( 'Upload', 'listify' ),
				'Add file' => __( 'Add file', 'listify' ),
				'Guest user' => __( 'Guest user', 'listify' ),
				'Showing %s' => __( 'Showing %s', 'listify' ),
				'Showing all %s' => __( 'Showing all %s', 'listify' ),
				'located in &quot;%s&quot;' => __( 'located in &quot;%s&quot;', 'listify' ),
				'No results found' => __( 'No results found', 'listify' ),
				'Query limit reached' => __( 'Query limit reached', 'listify' ),
				'Geocoding error' => __( 'Geocoding error', 'listify' ),
				'Employer' => __( 'Employer', 'listify' ),
				'Search %s' => __( 'Search %s', 'listify' ),
				'All %s' => __( 'All %s', 'listify' ),
				'Parent %s' => __( 'Parent %s', 'listify' ),
				'Parent %s:' => __( 'Parent %s:', 'listify' ),
				'Edit %s' => __( 'Edit %s', 'listify' ),
				'Update %s' => __( 'Update %s', 'listify' ),
				'Add New %s' => __( 'Add New %s', 'listify' ),
				'New %s Name' => __( 'New %s Name', 'listify' ),
				'Add New' => __( 'Add New', 'listify' ),
				'Add %s' => __( 'Add %s', 'listify' ),
				'New %s' => __( 'New %s', 'listify' ),
				'View %s' => __( 'View %s', 'listify' ),
				'No %s found' => __( 'No %s found', 'listify' ),
				'No %s found in trash' => __( 'No %s found in trash', 'listify' ),
				'This is where you can create and manage %s.' => __( 'This is where you can create and manage %s.', 'listify' ),
				'Expired' => __( 'Expired', 'listify' ),
				'Expired (%s)' => __( 'Expired (%s)', 'listify' ),
				'Invalid ID' => __( 'Invalid ID', 'listify' ),
				'This position has already been filled' => __( 'This position has already been filled', 'listify' ),
				'%s has been filled' => __( '%s has been filled', 'listify' ),
				'This position is not filled' => __( 'This position is not filled', 'listify' ),
				'%s has been marked as not filled' => __( '%s has been marked as not filled', 'listify' ),
				'%s has been deleted' => __( '%s has been deleted', 'listify' ),
				'Title' => __( 'Title', 'listify' ),
				'Date Posted' => __( 'Date Posted', 'listify' ),
				'Date Expires' => __( 'Date Expires', 'listify' ),
				'Load more listings' => __( 'Load more listings', 'listify' ),
				'Recent %s' => __( 'Recent %s', 'listify' ),
				'Keyword' => __( 'Keyword', 'listify' ),
				'Number of listings to show' => __( 'Number of listings to show', 'listify' ),
				'Invalid listing' => __( 'Invalid listing', 'listify' ),
				'Save changes' => __( 'Save changes', 'listify' ),
				'Your changes have been saved.' => __( 'Your changes have been saved.', 'listify' ),
				'View &rarr;' => __( 'View →', 'listify' ),
				'Submit Details' => __( 'Submit Details', 'listify' ),
				'Preview' => __( 'Preview', 'listify' ),
				'Done' => __( 'Done', 'listify' ),
				'Application email' => __( 'Application email', 'listify' ),
				'you@yourdomain.com' => __( 'you@yourdomain.com', 'listify' ),
				'Application URL' => __( 'Application URL', 'listify' ),
				'http://' => __( 'http://', 'listify' ),
				'Enter an email address or website URL' => __( 'Enter an email address or website URL', 'listify' ),
				'Description' => __( 'Description', 'listify' ),
				'Enter the name of the company' => __( 'Enter the name of the company', 'listify' ),
				'Website' => __( 'Website', 'listify' ),
				'Tagline' => __( 'Tagline', 'listify' ),
				'Briefly describe your company' => __( 'Briefly describe your company', 'listify' ),
				'Video' => __( 'Video', 'listify' ),
				'A link to a video about your company' => __( 'A link to a video about your company', 'listify' ),
				'Twitter username' => __( 'Twitter username', 'listify' ),
				'@yourcompany' => __( '@yourcompany', 'listify' ),
				'Logo' => __( 'Logo', 'listify' ),
				'%s is a required field' => __( '%s is a required field', 'listify' ),
				'%s is invalid' => __( '%s is invalid', 'listify' ),
				'Please enter a valid application email address' => __( 'Please enter a valid application email address', 'listify' ),
				'Please enter a valid application URL' => __( 'Please enter a valid application URL', 'listify' ),
				'Please enter a valid application email address or URL' => __( 'Please enter a valid application email address or URL', 'listify' ),
				'Preview &rarr;' => __( 'Preview &rarr;', 'listify' ),
				'You must be signed in to post a new listing.' => __( 'You must be signed in to post a new listing.', 'listify' ),
				'Submit Listing →' => __( 'Submit Listing →', 'listify' ),
				'&larr; Edit listing' => __( '&larr; Edit listing', 'listify' ),
				'\%s\ (filetype %s) needs to be one of the following file types: %s' => __( '\%s\ (filetype %s) needs to be one of the following file types: %s', 'listify' ),
				'Your account' => __( 'Your account', 'listify' ),
				'You are currently signed in as %s.' => __( 'You are currently signed in as %s.', 'listify' ),
				'Sign out' => __( 'Sign out', 'listify' ),
				'Have an account?' => __( 'Have an account?', 'listify' ),
				'Sign in' => __( 'Sign in', 'listify' ),
				'optionally' => __( 'optionally', 'listify' ),
				'You must sign in to create a new listing.' => __( 'You must sign in to create a new listing.', 'listify' ),
				'Your email' => __( 'Your email', 'listify' ),
				'(optional)' => __( '(optional)', 'listify' ),
				'%s ago' => __( '%s ago', 'listify' ),
				'No more results found.' => __( 'No more results found.', 'listify' ),
				'Posted %s ago' => __( 'Posted %s ago', 'listify' ),
				'This position has been filled' => __( 'This position has been filled', 'listify' ),
				'This listing has expired' => __( 'This listing has expired', 'listify' ),
				'remove' => __( 'remove', 'listify' ),
				'or' => __( 'or', 'listify' ),
				'Maximum file size: %s.' => __( 'Maximum file size: %s.', 'listify' ),
				'Apply using webmail:' => __( 'Apply using webmail:', 'listify' ),
				'Apply for job' => sprintf( __( 'Apply for %s', 'listify' ), $this->label( 'singular' ) ),
				'You need to be signed in to manage your listings.' => __( 'You need to be signed in to manage your listings.', 'listify' ),
				'You do not have any active listings.' => __( 'You do not have any active listings.', 'listify' ),
				'Mark not filled' => __( 'Mark not filled', 'listify' ),
				'Mark filled' => __( 'Mark filled', 'listify' ),
				'Relist' => __( 'Relist', 'listify' ),
				'Keywords' => __( 'Keywords', 'listify' ),
				'Category' => __( 'Category', 'listify' ),
				'Any category' => __( 'Any category', 'listify' ),
				'Company Details' => __( 'Company Details', 'listify' ),
				'%s submitted successfully. Your listing will be visible once approved.' => __( '%s submitted successfully. Your listing will be visible once approved.', 'listify' ),
				'Draft' => __( 'Draft', 'listify' ),
				'Preview' => __( 'Preview', 'listify' ),
				'Pending approval' => __( 'Pending approval', 'listify' ),
				'Pending payment' => __( 'Pending payment', 'listify' ),
				'Active' => __( 'Active', 'listify' ),
				'Reset' => __( 'Reset', 'listify' ),
				'RSS' => __( 'RSS', 'listify' ),
				'Your email address isn’t correct.' => __( 'Your email address isn’t correct.', 'listify' ),
				'This email is already registered, please choose another one.' => __( 'This email is already registered, please choose another one.', 'listify' ),
				'Choose a category…' => __( 'Choose a category…', 'listify' ),
				'Inactive' => __( 'Inactive', 'listify' ),
				'Application via \%s\ listing on %s' => __( 'Application via \%s\ listing on %s', 'listify' ),
				'Anywhere' => __( 'Anywhere', 'listify' ),
				'Are you sure you want to delete this listing?' => __( 'Are you sure you want to delete this listing?', 'listify' ),
			),
			'wp-job-manager-tags' => array(
				'Job Tags' => sprintf( '%s Tags', $this->label( 'singular' ) ),
				'Job tags' => sprintf( '%s Tags', $this->label( 'singular' ) ),
				'job-tag' => sprintf( '%s-tag', $this->label( 'singular', true ) ),
				'Comma separate tags, such as required skills or technologies, for this job.' => '',
				'Choose some tags, such as required skills or technologies, for this job.' => __( 'Choose some tags, such as required skills available features, for this listing.', 'listify' ),
				'Filter by tag:' => '<span class="filter-label">' . __( 'Filter by tag: ', 'listify' ) . '</span>',

				'Maximum Job Tags' => sprintf( 'Maximum %s Tags', $this->label( 'singular' ) )
			),
			'wp-job-manager-locations' => array(
				'Job Regions' => sprintf( '%s Regions', $this->label( 'singular' ) ),
				'Job Region' => sprintf( '%s Region', $this->label( 'singular' ) ),
				'job-region' => sprintf( '%s-region', $this->label( 'singular', true ) ),

				'Display a list of job regions.' => sprintf( __( 'Display a list of %s regions.', 'listify' ), $this->label( 'singular', true ) ),
			),
			'wp-job-manager-wc-paid-listings' => array(
				'%s job posted out of %d' => __( '%s listing posted out of %d', 'listify' ),
				'%s jobs posted out of %d' => __( '%s listings posted out of %d', 'listify' ),
				'%s for %d job' => __( '%s for %d listing', 'listify' ),
				'%s for %s jobs' => __( '%s for %s listings', 'listify' ),
				'Job Package' => sprintf( __( '%s Package', 'listify' ), $this->label( 'singular' ) ),
				'Job Package Subscription' => sprintf( __( '%s Package Subscription', 'listify' ), $this->label(
				'singular' ) ),
				'Job Listing' => sprintf( __( '%s Listing', 'listify' ), $this->label( 'singular' ) ),
				'Job listing limit' => sprintf( __( '%s limit', 'listify' ), $this->label( 'singular' ) ),
				'Job listing duration' => sprintf( __( '%s duration', 'listify' ), $this->label( 'singular' ) ),
				'The number of days that the job listing will be active.' => sprintf( __( 'The number of days that the %s
				will be active', 'listify' ), $this->label( 'singular', true ) ),
				'Feature job listings?' => sprintf( __( 'Feature %s?', 'listify' ), $this->label( 'singular', true ) ),
				'Feature this job listing - it will be styled differently and sticky.' => sprintf( __( 'Feature this %s
				-- it will be styled differently and sticky.', 'listify' ), $this->label( 'singular', true ) ),
				'My job packages' => sprintf( __( 'My %s packages', 'listify' ), $this->label( 'singular', true ) ),
				'Jobs Remaining' => sprintf( __( '%s Remaining', 'listify' ), $this->label( 'plural' ) )
			),
			'wp-job-manager-simple-paid-listings' => array(
				'Job #%d Payment Update' => __( '#%d Payment Update', 'listify' )
			),
			'wp-job-manager-alrts' => array(
				'Job Alert Results Matching \"%s\"' => __( 'Alert Results Matching \"%s\"', 'listify' ),
				'No jobs were found matching your search. Login to your account to change your alert criteria' => __( 'No
				results were found matching your search. Login to your account to change your alert criteria', 'listify'
				),
				'This job alert will automatically stop sending after %s.' => __( 'This alert will automatically stop
				sending after %s.', 'listify' ),
				'No jobs found' => sprintf( __( 'No %s found', 'listify' ), $this->label( 'plural', true ) ),
				'Optionally add a keyword to match jobs against' => sprintf( __( 'Optionally add a keyword to match %s 
				against', 'listify' ), $this->label( 'plural', true ) ),
				'Job Type' => sprintf( __( '%s Type', 'listify' ), $this->label( 'singular' ) ),
				'Any job type' => sprintf( __( 'Any %s type', 'listify' ), $this->label( 'singular', true ) ),
				'Job Type:' => sprintf( __( '%s Type:', 'listify' ), $this->label( 'singular' ) ),
				'Your job alerts are shown in the table below. Your alerts will be sent to %s.' => __( 'Your alerts are
				shown in the table below. The alerts will be sent to %s.', 'listify' )
			)
		);

		$this->strings = apply_filters( 'listify_strings', $strings );

		return $this->strings;
	}

}

$GLOBALS[ 'listify_strings' ] = new Listify_Strings();
