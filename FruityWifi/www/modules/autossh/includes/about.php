<b>autossh</b> is a program to start a copy of ssh and monitor it, restarting it as necessary should it die or stop passing traffic.

<br><br>The original idea and the mechanism were from rstunnel (Reliable SSH Tunnel). With version 1.2 of autossh the method changed: autossh uses ssh to construct a loop of ssh forwardings (one from local to remote, one from remote to local), and then sends test data that it expects to get back. (The idea is thanks to Terrence Martin.)

<br><br>With version 1.3, a new method is added (thanks to Ron Yorston): a port may be specified for a remote echo service that will echo back the test data. This avoids the congestion and the aggravation of making sure all the port numbers on the remote machine do not collide. The loop-of-forwardings method remains available for situa‐ tions where using an echo service may not be possible.
	 
<br><br><b>Author: </b>autossh was written by Carson Harding

<br>
<span style="font-family: courier">
<br><b>Example:</b> SSH:22
<br>
<br>[User  &nbsp;] reverse
<br>[Host  &nbsp;] 192.168.0.240
<br>[Port  &nbsp;] 22	
<br>[Listen] 2222
<br>
<br>FruityWifi --------> reverse@192.168.0.240 [2222]
<br>
<br>FruityWifi <-------- ssh -p 2222 user@localhost
<br>
<br>
<br><b>Example:</b> HTTP:8000 (FruityWifi HTTP)
<br>
<br>[User  &nbsp;] reverse
<br>[Host  &nbsp;] 192.168.0.240
<br>[Port  &nbsp;] 8000
<br>[Listen] 8888
<br>
<br>FruityWifi --------> reverse@192.168.0.240 [8888]
<br>
<br>FruityWifi <-------- wget/browser http://localhost:8888
</span>