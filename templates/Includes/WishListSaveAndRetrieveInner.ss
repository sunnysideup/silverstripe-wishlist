<div class="wishListHolder"><% if WishListMessage %><span class="wishListMessage">$WishListMessage</span><% end_if %></div>
<ul class="wishListSaveAndRetrieve">
	<% if CanSaveWishList %><li><a href="{$Link}savewishlist" class="wishListSave">save current wish list  ($NumberOfItemsInSessionOnes)</a></li><% end_if %>
	<% if CanRetrieveWishList %><li><a href="{$Link}retrievewishlist" class="wishListRetrieve">retrieve saved wish list ($NumberOfItemsInSavedOnes)</a></li><% end_if %>
	<% if CanClearWishList %><li><a href="{$Link}clearwishlist" class="wishListClear">clear my wish lists</a></li><% end_if %>
</ul>
