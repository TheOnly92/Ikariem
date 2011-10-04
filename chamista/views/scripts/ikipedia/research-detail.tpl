{include file="ikipedia/civilopedia_menu.tpl"}

<!---------------------------------------------------------------------------------------
	 ////////////////////////////////////////////////////////////////////////////////////
	 ///////////////// the main view. take care that it stretches. //////////////////////
	 //////////////////////////////////////////////////////////////////////////////////// --> 
<div id="mainview"> 
	<div class="buildingDescription"> 
		<h1 style="padding:0; margin:30px 0px 0px 0px;">Field of Research - {$researchDetail.field}</h1> 
	</div> 
	<div class="contentBox01h"> 
		<h3 class="header"><span class="textLabel"></span></h3> 
		<div class="content"> 
			<table cellpadding="0" cellspacing="0"> 
				<tr> 
					<td class="desc" title="Field of Research">Field of Research:</td> 
					<td class="" title="Seafaring">{$researchDetail.field}</td> 
				</tr> 
				<tr> 
					<td class="desc" title="Name">Name:</td> 
					<td class="" title="Deck Weapons">{$researchDetail.name}</td> 
				</tr> 
				<tr> 
					<td class="desc" title="Description">Description:</td> 
					<td class="" title="{$researchDetail.desc}">{$researchDetail.desc}</td> 
				</tr> 
				<!-- 
				<tr>
					<td class="desc" title="">:</td>
					<td class="" title=""></td>
				</tr> --> 
				<tr> 
					<td class="desc" title="Current research time">Current research time:</td> 
					<td class="">{$researchDetail.time}</td> 
				</tr> 
				<tr> 
					<td class="desc" title="Next research in this field">Next research in this field:</td> 
					<td class=""> 
						<a title="To the research of {$researchDetail.next.name}" href="/ikipedia/researchDetail?researchId={$researchDetail.next.id}">{$researchDetail.next.name}</a> 
					</td> 
				</tr> 
				<tr> 
					<td class="desc" title="Requirement(s)">Requirement(s):</td> 
					<td class="" title="Militaristic Future"> 
						<ul> 
							{if !$researchDetail.required1 && !$researchDetail.required2 && !$researchDetail.required3 && !$researchDetail.required4}
							-
							{else}
							{if $researchDetail.required1}
							<li><a title="To the research of {$researchDetail.required1.name}" href="/ikipedia/researchDetail?researchId={$researchDetail.required1.id}">{$researchDetail.required1.name}</a> ({$researchDetail.required1.field})</li>
							{/if}
							{if $researchDetail.required2}
							<li><a title="To the research of {$researchDetail.required2.name}" href="/ikipedia/researchDetail?researchId={$researchDetail.required2.id}">{$researchDetail.required2.name}</a> ({$researchDetail.required2.field})</li>
							{/if}
							{if $researchDetail.required3}
							<li><a title="To the research of {$researchDetail.required3.name}" href="/ikipedia/researchDetail?researchId={$researchDetail.required3.id}">{$researchDetail.required3.name}</a> ({$researchDetail.required3.field})</li>
							{/if}
							{if $researchDetail.required4}
							<li><a title="To the research of {$researchDetail.required4.name}" href="/ikipedia/researchDetail?researchId={$researchDetail.required4.id}">{$researchDetail.required4.name}</a> ({$researchDetail.required4.field})</li>
							{/if}
							{/if}
						</ul> 
					</td> 
				</tr> 
			</table> 
		</div> 
		<div class="footer"></div> 
	</div><!--contentBox01h--> 
</div><!-- end mainview --> 