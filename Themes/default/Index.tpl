{include file='Header.tpl'}
{include file='Sidebar.tpl'}
<!-- Begin Right Column -->
<div id="main">
{if $postcount_main > 0}
	{while var=$postcount_main}
		<div id="postbox">
			<h2 class="post-title"><a class="post-title" href="{$site_url}Post.php?id={loadpost name=id}">{loadpost name=title}</a></h2>
			<p class="post">{loadpost name=post}</p>
		</div>
	{/while}
{else}
{l v="NoPosts"}
{/if}
</div>
{include file='Footer.tpl'}
