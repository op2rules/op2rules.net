<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if($_SESSION['logged']){ ?>
<div style="font-size: 12px">
<table><tr><td style="width: 200px"><center>
<pre style="font-size: 8px">_______ _______ _______ _______ _______
|  ____ |_____| |  |  | |______ |______
|_____| |     | |  |  | |______ ______|</pre>
</center></td><td style="width: 660px">
Tyrain 2000 [ <a href="/files/Tyrain2000.exe">Tyrain2000.exe </a>] [ <a href="/files/Tyrain2000.dmg">Tyrain2000.dmg </a>]
<br>Star Wars Jedi Knight II : Jedi Outcast [ <a href="/files/Jedi Outcast.rar">Jedi Outcast.rar</a> ]
<br>Cossacks Back to War [ <a href="/files/cossacks.rar">cossacks.rar</a> ]
</td></tr></table>

<table><tr><td style="width: 200px"><center>
<pre style="font-size: 8px">                 _ _       
  /\/\   ___  __| (_) __ _ 
 /    \ / _ \/ _` | |/ _` |
/ /\/\ |  __| (_| | | (_| |
\/    \/\___|\__,_|_|\__,_|</pre></td><td style="width: 660px">
8tracks Playlist Downloader [ <a href="/files/8hacks.exe">8hacks.exe</a> ]
<br>Microsoft Office 2007 Enterprise Blue Edition [ <a href="/files/dgl-moebe.iso">dgl-moebe.iso </a>]
<br>Media Player Classic [ <a href="/files/MPC-HC.1.7.7.x64.zip">MPC-HC.1.7.7.x64.zip</a> ] [ <a href="/files/MPC-HC.1.7.7.x86.zip">MPC-HC.1.7.7.x86.zip</a> ]
<br>OReilly eBook collection [ <a href="/files/OReilly.zip">OReilly.zip</a> ]
</td></tr></table>

<table><tr><td style="width: 200px"><center>
<pre style="font-size: 8px"> _______                      
|   _   |.-----..-----..-----.
|       ||  _  ||  _  ||__ --|
|___|___||   __||   __||_____|
|__|   |__|</pre>
</center></td><td style="width: 660px">
Terminal Emulator for SCP, SSH, Telnet, and Sockets [ <a href="files/putty.exe">putty.exe</a> ] 
<br>Volume Space Usage [ <a href="files/TreeSizeFree.exe">TreeSizeFree.exe</a> ] [ <a href="files/TreeSizeFree.chm">TreeSizeFree.chm</a> ]
<br>WinRAR 4.00 32Bit/64Bit [ <a href="files/winrar.zip">winrar.zip</a> ]
<br>Webcam/Camera Viewer & Recorder v1.00 [ <a href="files/AMCAP2.EXE">AMCAP2.EXE</a> ] 
<br>Mouse Sample Rate Checker 1.1b [ <a href="files/mouserate.exe">mouserate.exe</a> ]
<br>Internet Relat Chat - mIRC 6.34 + NNS [ <a href="files/mIRC.rar">mIRC.rar</a> ]
<br>HTML color viewer [ <a href="files/colorpix.zip">colorpix.zip</a> ]
</td></tr></table>
</div> 
<br>

<?php } else { ?>
<pre>
Unfortunately there aren't many files yet, but if you want you can suggest some on IRC! 
Login to see member files.
</pre>
<?php } 
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>