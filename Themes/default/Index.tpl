{include file='Header.tpl'}
{include file='Sidebar.tpl'}
{if $postcount > 0}
	{while ($postcount > 0)}
		<div id="postbox">
			<h2 class="post-title"><a class="post-title" href="{$site_url}Post.php?id={loadpost name=id id=$postcount}">{loadpost name=title id=$postcount}</a></h2>
			<p class="post">{loadpost name=post id=$postcount}</p>
		</div>
		{assign var="postcount" value="`$postcount-1`"}
	{/while}
{else}
{l v="NoPosts"}
{/if}
{include file='Footer.tpl'}
