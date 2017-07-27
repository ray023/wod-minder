back to [ReadMe](https://github.com/ray023/wod-minder/blob/master/README.md)

# WOD-Minder Post Mortem
<ol>
	<li><b>Project</b></li>
	<ol>
		<li><b>Description</b></li>
			<ol>
				<li><b>Project Name:</b>  WOD-Minder</li>
				<li><b>Start Date:</b>  September 2, 2012</li>
			</ol>
		<li><b>Project Charter</b></li>
			<ol>
				<li><b>Reason for undertaking the project:</b>  To resolve redundant tasks and provide reporting capabilities for CrossFit Owners and Athletes.  In 2012, there were no software leaders in the CrossFit universe alleviating those tasks.</li>
				<li><b>Objectives</b></li>
					<ol>
					<li><b>For the CrossFit Athlete</b></li>
						<ol>
							<li>Log performance of:</li>
								<ol>
									<li>Weightlifting </li>
									<li>Workouts (a.k.a. WoDs)</li>
								</ol>
							<li>Reports to view progress and set goals</li>
							<li>Easy means to calculate</li>
								<ol>
									<li>Weights on the bar</li>
									<li>Percentages of Maxes</li>
								</ol>
							<li>Mobile solution</li>
						</ol>
					<li><b>For the Gym Owner</b></li>
						<ol>
							<li>Easy means to save workouts:</li>
								<ol>
									<li>Preloaded workouts for the all the CrossFit benchmarks wods (e.g. MURPH, Grace, etc.)</li>
									<li>Simple interface to save WoDs.</li>
								</ol>
							<li>Publish workouts from one source but reach every media platform</li>
							<li>Leaderboard (who’s on top)</li>
							<li>Athlete Attendance (for retention)</li>
						</ol>
					</ol>
				<li><b>Target Audience</b></li>
					<ol>
						<li>Any CrossFit gym owner </li>
						<li>Any CrossFit athlete</li>
					</ol>
				<li><b>Success Criterion</b></li>
				<li>Users who find the application easy to use and beneficial to their CrossFit lifestyle</li>
				<li>Gym owners who rely on the tools WOD-Minder provides to uphold athlete performance and morale</li>
			</ol>
		<li><b>Technologies Employed</b></li>
			<ol>
				<li><b>Server</b></li>
					<ol>
						<li><b>OS:</b>  Debian Linux </li>
						<li><b>Host:</b>  Rackspace</li>
						<li><b>RAM:</b>  256MB RAM</li>
						<li><b>Space:</b>  10GB</li>
					</ol>
				<li><b>Database:</b>  MySql</li>
				<li><b>Coding Languages</b></li>
					<ol>
						<li>PHP</li>
						<li>JavaScript</li>
					</ol>
				<li><b>Frameworks</b></li>
					<ol>
						<li><b>CodeIgniter:</b>  Chose this MVC framework because of its out-of-the-box features.  I liked everything about it except for the query builder.  There was nothing wrong with it.   I just prefer to build my own queries. </li>
						<li><b>jQuery Mobile:</b>  This framework was new in 2012 but made design for a mobile devices very easy.  </li>
						<li><b>Facebook PHP API:</b>  Used this to post content to Gym’s Facebook pages.  I have not updated to Graph so this feature is currently obsolete.</li>
					<ol>
				<li><b>Code Repository</b></li>
					<ol>
						<li><b>Type:</b>  Subversion</li>
						<li><b>Host:</b>  http://www.unfuddle.com </li>
					</ol>
				<li><b>Other Mentions</b></li>
					<ol>
						<li>HTML5 Canvas Element for the Barbell Calculator</li>
						<li>Scheduled CronJob for nightly database backups to DropBox account</li>
						<li>Google Analytics to gather stats</li>
					</ol>
			</ol>
		<li><b>Cost</b></li>
			<ol>
				<li>Domain Registration/Renewal:  $40/year</li>
				<li>Rackspace Hosting: ~$12/month</li>
			</ol>
	</ol>
	<li><b>Performance</b></li>
		<ol>
			<li><b>Key Accomplishments</b>, in order of importance </li>
				<ol>
					<li><b>Simple Performance Tracking:</b>  Saving WoDs/Maxes only takes seconds; retrieving that information is easy too.</li>
					<li><b>Quick Math:</b>  Calculating weights on the bar with the barbell calculator feature</li>
					<li><b>CrossFit Summary:</b> A report of CrossFit reports; it shows the athlete how much progress they have made and where they need to go</li>
					<li><b>Core following:</b>  There is a small group of people (most of whom I don’t even know) that use the application religiously.</li>
				</ol>
			<li><b>Key Problem Areas</b>, in order of importance</li>
				<ol>
					<li><b>Browser-based Web App:</b>  Users understand “app”; not “Web-Based mobile application”.  Telling a user to open the web browser on their phone, go to a URL, register for the site and then bookmark the page is too many steps.  </li>
					<li><b>No sales experience:</b>  Initiating contact with gym owners made me feel like the nerd asking out the prettiest girl in the school.  It was easier to approach gym owners I knew, but cold calls were tough.  </li>
					<li><b>User retention after signing up:</b>  Over three years, 345 users registered for the site.  A large percentage of those users (around 7%) would register, click a few pages and then never come back.  I saw this problem early-on but never figured out why people bailed so quickly.    </li>
					<li><b>First gym to use the product went to a competitor:</b>  I never got official endorsement from the box owner of the first gym, but I did accumulate an active client base through word of mouth.  At its peak, around 80 members a day for just that one box saved their information.  Seeing WOD-Minder come to life with activity was rewarding in itself.  It also helped me see how users used the product; this helped improve its functionality.  I made several attempts to meet with the owner of the gym, but I never could sit him down to show him its capabilities.  I assumed he would see how many of his members used it and then try to contact me.  In my mind, I reasoned an in-house developer building a custom solution made more sense than paying $300 a month for a different product; bad assumption.   </li>
					<li><b>The name:</b>  I don’t even like the name “WOD-Minder”.  It was a spin on some older products I had worked on in the past that had “-Minder” suffixed to the end of it.  Minder is a British term that means a person whose job it is to look after someone or something.  The definition was applicable, but I didn’t like it.  However, I don’t think the name played a crucial role for the app’s success or failure.  I just wanted something easy to remember and something easy to find via search engine.</li>
					<li><b>Processes that didn’t work well</b></li>
						<ol>
							<li>I used the basic template of jQuery Mobile to build the app.  I love this framework, but it is definitely no frills.  I kept the black and gray default pattern.  This made the app look boring.  </li>
							<li>The UI became clunky over time.  Saving the daily wod or max was really easy but navigation to other functionality was difficult and not intuitive.</li>
						</ol>
				</ol>
		</ol>
	<li><b>Key Lessons Learned</b></li>
		<ol>
			<li>If only I had the experience and tools three years ago that I have now.  (Always the case, right?  ).  Here’s how I would start today:</li>
				<ol>
					<li>Use Microsoft .NET technologies to build the Website and Web API</li>
						<ol>
							<li>Code First design; designing classes that follow good design practices and are easily testable.  </li>
							<li>MVC Razor </li>
						</ol>
					<li>ii.	Use SQL Server for the database; (I like MySQL but feel more comfortable using SQL Server.)</li>
					<li>iii.	Microsoft Azure Services for </li>
						<ol>
							<li>Hosting the WebSite and WEB API </li>
							<li>A SQL Server for the database.  </li>
						</ol>
					<li>iv.	Athletes would have an App</li>
						<ol>
							<li>Using OAuth authentication for quick, easy login</li>
							<li>Ionic Framework for an attractive, user-friendly UI; also allows one code base to be published to all the mobile stores</li>
						</ol>
					<li>Gym Owners would have a Web Interface for all their management. </li>
				</ol>
			<li>You get what you pay for; at least with web-hosting.  To save money, I went with a low-cost web-hosting company.  When I ran into issues requiring outside support, it cost me a lot of time trying to resolve them.  So much to the point where I ended up setting up and using my own server at Rackspace.  </li>
			<li>Don’t spend hours designing/coding very specific features for one potential client.  I started speaking with and attained the interest of the Number 2 guy at the largest CrossFit gym in the Southeast.  He was unhappy with their current product and wanted something more specific to their needs.  We worked together for about two months adapting WOD-Minder to fit those needs.  He provided a lot of positive feedback and all but swore on his life that approval would be simple.  In the end, when it came time for final approval, the owner shut it down.  </li>
		</ol>
	<li><b>Summary</b></li>
		<ol>
		<li>WOD-Minder has been a valuable and fun learning experience through all aspects of development.  My goal was to create an application I could use for my own benefit; but carry it forward to others who needed the same set of tools.  Career experience gave me a good starting point, but it was my passion that turned this idea into a living entity.  </li>
		</ol>
</ol>
