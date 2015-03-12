function initializePortfolio() {

	'use strict';

	var current,
		next, 
		prev,
		target, 
		hash,
		url,
		page,
		title,	  	  	  
		projectIndex,
		scrollPostition,
		projectLength,
		ajaxLoading = false,
		wrapperHeight,
		pageRefresh = true,
		content =false,
		correction = 30,
		headerH = jQuery('.logo').height()+correction,
		portfolioGrid = jQuery('.projectlist'),
		easing = 'easeOutExpo',
		folderName =''; // projects

		jQuery('.project-navigation ul').hide();
		jQuery('.closeProject a').hide();

	jQuery(window).bind( 'hashchange', function() {
		hash = jQuery(window.location).attr('hash'); 
		var root = '#!'+ folderName +'/';
		var rootLength = root.length;

		if( hash.substr(0,rootLength) != root ){
			return;						
		} else {	

			hash = jQuery(window.location).attr('hash'); 
			url = hash.replace(/[#\!]/g, '' );

			portfolioGrid.find('.project.current').children().removeClass('active');
			portfolioGrid.find('.project.current').removeClass('current');
			jQuery('.portfolio').find('.projectlist.active-folio').removeClass('active-folio');
			jQuery('.portfolio').find('.ajax-content.active-ajax, .project-navigation.active-ajax, .closeProject.active-ajax, .loader.active-ajax').removeClass('active-ajax');

			portfolioGrid.find('.project a[href="#!' + url + '"]' ).parent().addClass( 'current' );
		 	portfolioGrid.find('.project.current').find('a[href="#!' + url + '"]').addClass('active');

		 	portfolioGrid.find('.project a[href="#!' + url + '"]' ).parents('.projectlist').addClass( 'active-folio' );
		 	jQuery('.active-folio').siblings('.ajax-section').children('.ajax-content, .project-navigation, .closeProject, .loader').addClass('active-ajax');

		 	var projectContainer = jQuery('.ajax-content.active-ajax');
		 	var loader = jQuery('.loader.active-ajax');
		 	var projectNav = jQuery('.project-navigation.active-ajax ul');
		 	var exitProject = jQuery('.closeProject.active-ajax a');

			/* IF URL IS PASTED IN ADDRESS BAR AND REFRESHED */
			if(pageRefresh == true && hash.substr(0,rootLength) ==  root){	

				jQuery('html,body').stop().animate({scrollTop: (projectContainer.offset().top-20)+'px'},800,'easeOutExpo', function(){											
					loadProject();																									  
				});

			/* CLICKING ON PORTFOLIO GRID OR THROUGH PROJECT NAVIGATION */	
			}else if(pageRefresh == false && hash.substr(0,rootLength) == root){

				jQuery('html,body').stop().animate({scrollTop: (projectContainer.offset().top-headerH)+'px'},800,'easeOutExpo', function(){ 		
	
				if(content == false){						
					loadProject();							
				}else{	
					projectContainer.animate({opacity:0,height:wrapperHeight},function(){
						loadProject();
					});
				}
						
				projectNav.fadeOut('100');
				exitProject.fadeOut('100');
						
				});

			/* USING BROWSER BACK BUTTON WITHOUT REFRESHING */
			}else if(hash=='' && pageRefresh == false || hash.substr(0,rootLength) != root && pageRefresh == false || hash.substr(0,rootLength) != root && pageRefresh == true){	
		        scrollPostition = hash; 
				jQuery('html,body').stop().animate({scrollTop: scrollPostition+'px'},1000,function(){				
							
					deleteProject();								
							
				});
			}
	 	}
	});

	function loadProject(){
		var loader = jQuery('.loader.active-ajax');

		loader.fadeIn().removeClass('projectError').html('');


		if(!ajaxLoading) {				
			ajaxLoading = true;

			var projectContainer = jQuery('.ajax-content.active-ajax');

			projectContainer.load( url +' div#ajaxpage', function(xhr, statusText, request){

				if(statusText == "success"){				

				ajaxLoading = false;

				page = jQuery('#ajaxpage');

				jQuery('.slider').bxSlider({
					mode: 'horizontal',
					touchEnabled: true,
					swipeThreshold: 50,
					oneToOneTouch: true,
					pagerSelector: '.slider-pager',
					nextSelector: ".project-gallery-next",
					prevSelector: ".project-gallery-prev",
					nextText: "next",
					prevText: "prev",
					tickerHover: true
				});

				jQuery('#ajaxpage').waitForImages(function() {
				    hideLoader();  
				});

				jQuery(".container").fitVids();								  

				}

				if(statusText == "error"){

				loader.addClass('projectError').append(loadingError);

				loader.find('p').slideDown();

				}

			});

		}
			
	}

	function hideLoader(){
		var loader = jQuery('.loader.active-ajax');

		loader.delay(400).fadeOut( function(){													  
					showProject();					
			});			 
	}

	function showProject(){

		var projectContainer = jQuery('.ajax-content.active-ajax');
		var projectNav = jQuery('.project-navigation.active-ajax ul');
		var exitProject = jQuery('.closeProject.active-ajax a');

		wrapperHeight = projectContainer.children('#ajaxpage').outerHeight()+'px';
		
		if(content==false){

			wrapperHeight = projectContainer.children('#ajaxpage').outerHeight()+'px';

			projectContainer.animate({opacity:1,height:wrapperHeight}, function(){
				jQuery('.container').fitVids();
				scrollPostition = jQuery('html,body').scrollTop();
				projectNav.fadeIn();
				exitProject.fadeIn();
				content = true;	
				
			});

		} else {
			wrapperHeight = projectContainer.children('#ajaxpage').outerHeight()+'px';

			projectContainer.animate({opacity:1,height:wrapperHeight}, function(){
				jQuery('.container').fitVids();																		  
				scrollPostition = jQuery('html,body').scrollTop();
				projectNav.fadeIn();
				exitProject.fadeIn();

			});					
		}


		projectIndex = portfolioGrid.find('.project.current').index();
		projectLength = jQuery('.project a').length-1;


		if(projectIndex == projectLength){

			jQuery('.nextProject a').addClass('disabled');
			jQuery('.prevProject a').removeClass('disabled');

		} else if(projectIndex == 0) {

			jQuery('.prevProject a').addClass('disabled');
			jQuery('.nextProject a').removeClass('disabled');

		} else {

			jQuery('.nextProject a, .prevProject a').removeClass('disabled');

		}
	
  	}

  	function deleteProject(closeURL){

  		var projectContainer = jQuery('.ajax-content.active-ajax');
  		var projectNav = jQuery('.project-navigation.active-ajax ul');
  		var exitProject = jQuery('.closeProject.active-ajax a');

		projectNav.fadeOut();
		exitProject.fadeOut();

		if(typeof closeURL!='undefined' && closeURL!='') {
			window.location.hash = '#_';
		}

		projectContainer.animate({opacity:0,height:'0px'},800,'easeOutExpo');
		projectContainer.empty();
		jQuery('html,body').stop().animate({
			scrollTop: (projectContainer.offset().top-headerH-100)+'px'},600
		);

		jQuery('.portfolio').find('.projectlist.active-folio').removeClass('active-folio');
		jQuery('.portfolio').find('.ajax-content.active-ajax, .project-navigation.active-ajax, .closeProject.active-ajax').removeClass('active-ajax');
		portfolioGrid.find('.project.current').children().removeClass('active');
		portfolioGrid.find('.project.current').removeClass('current');			
 	}

 	jQuery('.nextProject a').on('click',function () {											   							   
					 
		current = portfolioGrid.find('.project.current');
		next = current.next('.project');
		target = jQuery(next).children('a').attr('href');
		jQuery(this).attr('href', target);

		if (next.length === 0) { 
			return false;			  
		} 

		current.removeClass('current'); 
		current.children().removeClass('active');
		next.addClass('current');
		next.children().addClass('active');
	   
	});

	jQuery('.prevProject a').on('click',function () {			
			
		current = portfolioGrid.find('.project.current');
		prev = current.prev('.project');
		target = jQuery(prev).children('a').attr('href');
		jQuery(this).attr('href', target);


		if (prev.length === 0) {
			return false;			
		}

		current.removeClass('current');  
		current.children().removeClass('active');
		prev.addClass('current');
		prev.children().addClass('active');

	});

	jQuery('.closeProject a').on('click',function () {

		var loader = jQuery('.loader.active-ajax'); 
							
		deleteProject(jQuery(this).attr('href'));					
		
		portfolioGrid.find('.project.current').children().removeClass('active');			
		loader.fadeOut();

		return false;
	});

	pageRefresh = false;
};


jQuery(document).ready(function(){
	initializePortfolio();
});
