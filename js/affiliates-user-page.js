var refTree = {};
jQuery(document).ready(function($) {
	new ClipboardJS('#get-aff-link .btn');

	// Fix conflict ahref tourmaster
	var ticker = 500;
	var fixConflict = setInterval(function(){
		$("#affiliates-user-page a[href]").off('click');
		if (ticker >= 16000) {
			clearInterval(fixConflict);	
		}
		ticker += 500;
	}, 500);
	
	// Treant tree
	if (typeof ReferralTreeConfig !== 'undefined') {
		refTree = new Treant( ReferralTreeConfig );
		$('#affiliates-nav-tab').on('click', '#nav-ref-tree', function(event) {
			event.preventDefault();
			/* Act on the event */
			refTree.destroy();
			setTimeout(function(){
				refTree = new Treant( ReferralTreeConfig );
			}, 500);
		});
	}
});