var refTree = {};
jQuery(document).ready(function($) {
	// Treant tree
	if (typeof ReferralTreeConfig !== 'undefined') {
		refTree = new Treant( ReferralTreeConfig );
	}
});