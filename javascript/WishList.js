/**
 *@author Nicolaas [at] sunnysideup.co.nz
 *@description: makes the add to wish list links ajax.
 **/

;(function($) {
	$(document).ready(
		function() {
			WishList.init();
		}
	);
})(jQuery);

var WishList = {

	holderSelector: ".addToWishListHolder",

	loadingClass: "loading",

	addLinkSelector: ".addToWishListLink",

	removeLinkSelector: ".removeFromWishListLink",

	fullListSelector: "#WishListList",

	fullListItemSelector: "#WishListList li, #WishList tr",

	saveAndRetrieveSelector: ".wishListSaveAndRetrieve",

	saveSelector: ".wishListSave",

	retrieveSelector: ".wishListRetrieve",

	clearSelector: ".wishListClear",

	noConfirmations: false,

	ConfirmDeleteText: "Are you sure you would like to remove this item from your wish list?",
	set_confirm_delete_text: function(v) {this.ConfirmDeleteText = v;},

	ConfirmRetrieveText: "Are you sure you would like to retrieve your saved list?  It will replace your current list.  Do you want to go ahead?",
	set_confirm_retrieve_text: function(v) {this.ConfirmRetrieveText = v;},

	ConfirmClearText: "Are you sure you would like to permanently delete your saved list?  ",
	set_confirm_clear_text: function(v) {this.ConfirmRetrieveText = v;},

	reloadListURL: "",
	set_reload_list_url: function(v) {this.reloadListURL = v;},

	init: function() {
		jQuery(WishList.holderSelector)
			.addWishListAddLinks()
			.addWishListRemoveLinks();
		jQuery(WishList.saveAndRetrieveSelector)
			.addWishListSaveLink()
			.addWishListRetrieveLink()
			.addWishListClearLink();
	},


	loadLinks: function( url, el ) {
		var clickedElement = el;
		jQuery(clickedElement).parents(WishList.holderSelector).addClass(WishList.loadingClass);
		jQuery.get(
			url,
			{},
			function(data, el) {
				jQuery(clickedElement).parents(WishList.holderSelector)
					.html(data)
					.removeClass(WishList.loadingClass)
					.addWishListAddLinks()
					.addWishListRemoveLinks();

			}
		);
		return true;
	},


	loadSaveAndRetrieve: function( url ) {
		jQuery(WishList.saveAndRetrieveSelector).addClass(WishList.loadingClass);
		jQuery.get(
			url,
			{},
			function(data, el) {
				jQuery(WishList.saveAndRetrieveSelector)
					.html(data)
					.removeClass(WishList.loadingClass);
					//not need because live() is used rather than click() when attached event handlers
					//.addWishListSaveLink()
					//.addWishListRetrieveLink();
			}
		);
		return true;
	},

	loadList: function( url ) {
		jQuery(WishList.fullListSelector).addClass(WishList.loadingClass);
		jQuery.get(
			url,
			{},
			function(data, el) {
				jQuery(WishList.fullListSelector)
					.html(data)
					.removeClass(WishList.loadingClass)
					.addWishListAddLinks()
					.addWishListRemoveLinks();
				if(WishList.reloadListURL) {
					WishList.loadSaveAndRetrieve(WishList.reloadListURL);
				}
				else {
					alert("no url available for reload");
				}
			}
		);
		return true;
	}

}


jQuery.fn.extend({

	addWishListAddLinks: function() {
		//using live() but on() should be used when jquery is upgraded to 1.7
		//if jquery is upgraded past 1.4.3 but not to 1.7 then delegate() should be used
		//more details here: http://api.jquery.com/live/
		jQuery(this).find(WishList.addLinkSelector).live('click',
			function(){
				var url = jQuery(this).attr("href");
				WishList.loadLinks(url, this);
				if(jQuery(this).parents(WishList.fullListItemSelector).length > 0) {
					jQuery(this).parents(WishList.fullListItemSelector).removeClass("strikeThrough");
				}

				return false;
			}
		);
		return this;
	},

	addWishListRemoveLinks: function () {
		jQuery(this).find(WishList.removeLinkSelector).live('click',
			function(){
				if(WishList.noConfirmations || confirm(WishList.ConfirmDeleteText)) {
					var url = jQuery(this).attr("href");
					WishList.loadLinks(url, this);
					//if it is part of a list, add line trough current item
					if(jQuery(this).parents(WishList.fullListItemSelector).length > 0) {
						jQuery(this).parents(WishList.fullListItemSelector).addClass("strikeThrough");
					}
				}
				return false;
			}
		);
		return this;
	},

	addWishListSaveLink: function () {
		jQuery(this).find(WishList.saveSelector).click(
			function(){
				var url = jQuery(this).attr("href");
				WishList.loadSaveAndRetrieve(url);
				return false;
			}
		);
		return this;
	},

	addWishListRetrieveLink: function () {
		jQuery(this).find(WishList.retrieveSelector).click(
			function(){
				if(WishList.noConfirmations || confirm(WishList.ConfirmRetrieveText)) {
					return true;
				}
				return false;
			}
		);
		return this;
	},

	addWishListClearLink: function () {
		jQuery(this).find(WishList.clearSelector).click(
			function(){
				if(WishList.noConfirmations || confirm(WishList.ConfirmClearText)) {
					return true
				}
				return false;
			}
		);
		return this;
	}

});

