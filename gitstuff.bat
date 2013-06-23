@ECHO OFF
IF "%1" EQU "" ( 
echo ERROR: Missing commit comments
echo     %0 "commit comments"
) ELSE ( 
	git remote add origin git@github.com:lizardtailgoescrazy/SA_ytplayer_chat.git
	git add *
	git commit -m "%1"
	git push -u origin master
)


