			<div id="sidebar">
				<div>
					<h4>Pages</h4>
					<ul>
					<?php while($page = $result10->fetchObject()) : ?>		
						<li><?php echo $page->title; ?></li>
					<?php endwhile; ?>	
					</ul>
				</div>
				
				<div>
					<h4>Meta</h4>
					<ul>
						<li><a href="<?php bloginfo('url') ?>admin/">Site Admin</a></li>
					</ul>
				</div>
				<div class="clear"></div>
			</div>
		</div>	   
