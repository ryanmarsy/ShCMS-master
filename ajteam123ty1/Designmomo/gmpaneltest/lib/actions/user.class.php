<?php

/**
 * @author		Momo
 * @copyright	2014 Jamiro Productions
**/

class userAction{
	private $gm;
	private $top;
	private $value;
	private $status;
	private $user;
	private $banned;
	private $isgm;
	private $chars;
	private $char;
	private $fraktion;
	private $gilde;
	private $banlin;
	private $degm;
	private $items;
	
	public function __construct(&$gm){
		$this->gm = $gm;
	}
	
	public function execute(){
		if (!isset($_GET['UserUID']) && !isset($_POST['Suche'])){
			if (isset($_GET['top'])){
				if (Parser::gint($_GET['top']))
					$this->top = Top::Accounts($_GET['top']);
				else
					$this->top = Top::Accounts(5000);
			}
			else
				$this->top = Top::Accounts(5000);
			
			//HTML
			echo '<center>';
			echo '<h2>gestion des comptes</h2>';
			echo '<br>';
			echo '<form action="index.php?action=user" method="POST">';
			echo '<input type="text" name="Suche" maxlength=18>';
			echo '<input type="submit" name="submit" value="Suchen" style="display: inline">';
			echo '</form>';
			echo '<br>';
			echo '<table>';
			echo '<tr>';
			echo '<th>UserUID</th><th>Name</th><th>Status</th><th>IP-Adresse</th><th>Letzer Login</th><th>AP</th>';
			echo '</tr>';
			
			foreach($this->top as $this->value){
				if ($this->value['Status'] > 15 && $this->value['Admin'] == 1 && $this->value['AdminLevel'] = 255)
					$this->status = 'Game Master';
				else if ($this->value['Status'] == -5)
					$this->status = 'Gebannt';
				else
					$this->status = 'Normal';
			
				echo '<tr>';
				echo '<td>'.Parser::Zahl($this->value['UserUID']).'</td>';
				echo '<td><a href="index.php?action=user&amp;UserUID='.$this->value['UserUID'].'">'.$this->value['UserID'].'</a></td>';
				echo '<td>'.$this->status.'</td>';
				echo '<td><a href="index.php?action=ip&amp;ip='.$this->value['UserIP'].'">'.$this->value['UserIP'].'</a></td>';
				echo '<td>'.Parser::Datum($this->value['JoinDate']).'</td>';
				echo '<td>'.Parser::zahl($this->value['Point']).'</td>';
				echo '</tr>';
			}
			
			//HTML
			echo '</table>';
			echo '</center>';
			
		} else if (isset($_POST['Suche'])){
			$this->top = Search::Accounts($_POST['Suche']);
			echo '<center>';
			echo '<h2>gestion des comptes</h2>';
			echo '<br>';
			echo '<form action="index.php?action=user" method="POST">';
			echo '<input type="text" name="Suche" maxlength=18>';
			echo '<input type="submit" name="submit" value="Suchen" style="display: inline">';
			echo '</form>';
			echo '<br>';
			echo '<table>';
			echo '<tr>';
			echo '<th>UserUID</th><th>Name</th><th>Status</th><th>IP-Adresse</th><th>Letzer Login</th><th>AP</th>';
			echo '</tr>';
			if ($this->top == false)
				echo '</table>La recherche n a donn� aucun r�sultat.</center>';
			else {
				foreach($this->top as $this->value){
					if ($this->value['Status'] > 15 && $this->value['Admin'] == 1 && $this->value['AdminLevel'] = 255)
						$this->status = 'Game Master';
					else if ($this->value['Status'] == -5)
						$this->status = 'Gebannt';
					else
						$this->status = 'Normal';
				
					echo '<tr>';
					echo '<td>'.Parser::Zahl($this->value['UserUID']).'</td>';
					echo '<td><a href="index.php?action=user&amp;UserUID='.$this->value['UserUID'].'">'.$this->value['UserID'].'</a></td>';
					echo '<td>'.$this->status.'</td>';
					echo '<td><a href="index.php?action=ip&amp;ip='.$this->value['UserIP'].'">'.$this->value['UserIP'].'</a></td>';
					echo '<td>'.Parser::Datum($this->value['JoinDate']).'</td>';
					echo '<td>'.Parser::zahl($this->value['Point']).'</td>';
					echo '</tr>';
				}
				echo '</table>';
				echo '</center>';
			}
			
		} else if (isset($_GET['UserUID'])){
			if (!Parser::gint($_GET['UserUID']))
				throw new SystemException('UserUID muss vom Typ integer sein', 0, 0, __FILE__, __LINE__);
			
			$this->user = new user($_GET['UserUID']);
			
			if (isset($_GET['do'])){
				switch($_GET['do']){
					case 'ban':
						if ($this->user->ban())
							echo '<div class="success">'.$this->user->getName().' est banni avec succ�s.</div>';
						break;
					case 'unban':
						if ($this->user->unban())
							echo '<div class="success">'.$this->user->getName().' a �t� d�banni succ�s.</div>';
						break;
					case 'pw':
						if (isset($_POST['PW'])){
							if ($this->user->changePw($_POST['PW']))
								echo '<div class="success">'.$this->user->getName().'s le mot de passe a �t� chang� avec succ�s.</div>';
						}
						else
								echo '<div class="error">S il vous pla�t, entrez votre mot de passe.</div>';
							
						break;
					case 'giveGM':
						if ($this->user->makeGM($_GET['Status'])){
							if ($_GET['Status'] > 0)
								echo '<div class="success">'.$this->user->getName().' est maintenant un GM.</div>';
							else
								echo '<div class="success">'.$this->user->getName().' maintenant n est plus GM .</div>';
						}
						break;
					case 'giveAp':
						if (isset($_POST['AP'])){
							if ($this->user->giveAp($_POST['AP']))
								echo '<div class="success">'.$this->user->getName().' �taient succ�s '.$_POST['AP'].'AP cr�dit�</div>';
						}
						break;
					case 'takeAp':
						if (isset($_POST['AP'])){
							if ($this->user->takeAp($_POST['AP']) != 0)
								echo '<div class="success">'.$this->user->getName().' �taient succ�s '.$_POST['AP'].'AP d�duit</div>';
							else
								echo '<div class="error">'.$this->user->takeAp($_POST['AP']).'</div>';
						}
						break;
				}
				$this->user = new user($_GET['UserUID']);
			}
			
			if ($this->user->isBanned()){
				$this->banned = 'oui';
				$this->banlin = '<a href="javascript::void();" id="unban">d�bannir</a>';
			} else{
				$this->banned = 'Non';
				$this->banlin = '<a href="javascript::void();" id="ban">Bannir</a>';
			}
			
			if ($this->user->isGm()){
				$this->isgm = 'oui ('.$this->user->getStatus().')';
				$this->degm = '<a href="javascript::void();" id="ungm">retirer GM</a>';
			} else{
				$this->isgm = 'Non';
				$this->degm = '';
			}
			
			if ($this->user->getFraktion() == 0)
				$this->fraktion = 'Light';
			else
				$this->fraktion = 'Dark';
			
			
			echo '<table>';
			echo '<tr><td>UserUID: </td><td>'.Parser::Zahl($this->user->getUserUID()).'</td><td></td><td></td></tr>';
			echo '<tr><td>Compte: </td><td>'.$this->user->getName().' et PW=   '.$this->user->getPw().'</td><td></td><td></td></tr>';
			
			echo '<tr><td>Password: </td><td>'.$this->user->getPw().'</td><td></td><td><a href="javascript::void()" id="pw">changer mot de passe</a></td></tr>';
			echo '<tr><td>IP-Adresse: </td><td><a href="index.php?action=ip&amp;ip='.$this->user->getIp().'">'.$this->user->getIp().'</a></td><td></td><td></td></tr>';
			echo '<tr><td>Faction: </td><td>'.$this->fraktion.'</td><td></td><td></td></tr>';
			echo '<tr><td>AP: </td><td>'.Parser::Zahl($this->user->getPoint()).'</td><td><a href="javascript::void();" id="apg">AP cr�dit</a></td><td><a href="javascript::void();" id="apa">AP enlever</a></td></tr>';
			echo '<tr><td>Banni: </td><td>'.$this->banned.'</td><td></td><td>'.$this->banlin.'</td></tr>';
			echo '<tr><td>GMr: </td><td>'.$this->isgm.'</td><td>'.$this->degm.'</td><td><a href="javascript::void();" id="gm">GM donner</a></td></tr>';
			echo '</table>';
			
			$this->chars = $this->user->getChars();
			
			
			
			echo '<br>';
			echo '<br>';
			echo 'Personnage';
			if (!$this->chars)
				echo '<br>Pas de Chars disponibles';
			else {
				echo '<table>';
				echo '<tr><th>Char ID</th><th>Char Name</th><th>Level</th><th>Classe</th><th>Guilde</th></tr>';
				foreach($this->chars as $this->value){
					$this->char = new char($this->value);
					if ($this->char->getGuild()){
						$this->gilde = new guild($this->char->getGuild());
						$this->gilde = $this->gilde->getName();
					} else
						$this->gilde = 'aucun';
					echo '<tr>';
					echo '<td>'.Parser::Zahl($this->char->getCharID()).'</td>';
					echo '<td><a href="index.php?action=char&amp;CharID='.$this->char->getCharID().'">'.$this->char->getName().'</a></td>';
					echo '<td>'.$this->char->getLevel().'</td>';
					echo '<td>'.$this->char->getClassName().'</td><td>';
					if ($this->char->getGuild())
						echo '<a href="index.php?action=guild&amp;GuildID='.$this->char->getGuild().'">';
					echo $this->gilde;
					if ($this->char->getGuild())
						echo '</a>';
					echo '</td></tr>';
				}
				echo '</table>';
			}
			
			$this->items = $this->user->leseWL();
			
			echo '<br><br><br>entrepot:<table>';
			echo '<tr>';
			echo '<th>ItemID</th><th>Name</th><th>Level</th><th>espace</th><th>Slots</th><th>durabilit�</th><th>Deposer le</th>';
			foreach($this->items as $this->value){
				$this->value['ToolTip'] = '';
				
				if ($this->value['Lapis1'] != 0){
					$this->value['TempLapis']	= new lapis($this->value['Lapis1']);
					$this->value['ToolTip'] 	.= 'Slot 1: '.$this->value['TempLapis']->getName().'<br>';
				}
				
				if ($this->value['Lapis2'] != 0){
					$this->value['TempLapis']	= new lapis($this->value['Lapis2']);
					$this->value['ToolTip'] 	.= 'Slot 2: '.$this->value['TempLapis']->getName().'<br>';
				}
				
				if ($this->value['Lapis3'] != 0){
					$this->value['TempLapis']	= new lapis($this->value['Lapis3']);
					$this->value['ToolTip'] 	.= 'Slot 3: '.$this->value['TempLapis']->getName().'<br>';
				}
				
				if ($this->value['Lapis4'] != 0){
					$this->value['TempLapis']	= new lapis($this->value['Lapis4']);
					$this->value['ToolTip'] 	.= 'Slot 4: '.$this->value['TempLapis']->getName().'<br>';
				}
				
				if ($this->value['Lapis5'] != 0){
					$this->value['TempLapis']	= new lapis($this->value['Lapis5']);
					$this->value['ToolTip'] 	.= 'Slot 5: '.$this->value['TempLapis']->getName().'<br>';
				}
				
				if ($this->value['Lapis6'] != 0){
					$this->value['TempLapis']	= new lapis($this->value['Lapis6']);
					$this->value['ToolTip'] 	.= 'Slot 6: '.$this->value['TempLapis']->getName().'<br>';
				}
				
				echo '<tr>';
				echo '<td>'.$this->value['ItemID'].'</td>';
				echo '<td><a href="index.php?action=wl&amp;uid='.$this->value['ItemUID'].'" class="tooltip" title="'.$this->value['ToolTip'].'">'.$this->value['ItemName'].'</a></td>';
				echo '<td>'.$this->value['ReqLevel'].'</td>';
				echo '<td>'.$this->value['Slot'].'</td>';
				echo '<td>'.$this->value['MaxSlot'].'</td>';
				echo '<td>'.$this->value['MaxQuality'].'</td>';
				echo '<td>'.Parser::Datum($this->value['MakeTime']).'</td>';
				echo '</tr>';
			}
			echo '</tr>';
			echo '</table>';
			
			?>
			<script type="text/javascript">
			 $(function(){
				$("#bandia").dialog({
					bgiframe: true,
					autoOpen: false,
					height: 300,
					modal: true,
					buttons: {
						Ja: function() {
							document.location.href = 'index.php?action=user&UserUID=<?php echo $this->user->getUserUID();?>&do=ban';
							$(this).dialog('close');
						},
					
						Nein: function() {
							$(this).dialog('close');
						}

					}

				});
				
				$("#unbandia").dialog({
					bgiframe: true,
					autoOpen: false,
					height: 300,
					modal: true,
					buttons: {
						Ja: function() {
							document.location.href = 'index.php?action=user&UserUID=<?php echo $this->user->getUserUID();?>&do=unban';
							$(this).dialog('close');
						},
					
						Nein: function() {
							$(this).dialog('close');
						}

					}

				});
				
				$("#gmdia").dialog({
					bgiframe: true,
					autoOpen: false,
					height: 300,
					width: 400,
					modal: true,
					buttons: {
						16: function() {
							document.location.href = 'index.php?action=user&UserUID=<?php echo $this->user->getUserUID();?>&do=giveGM&Status=16';
							$(this).dialog('close');
						},
						
						32: function() {
							document.location.href = 'index.php?action=user&UserUID=<?php echo $this->user->getUserUID();?>&do=giveGM&Status=32';
							$(this).dialog('close');
						},
						
						48: function() {
							document.location.href = 'index.php?action=user&UserUID=<?php echo $this->user->getUserUID();?>&do=giveGM&Status=48';
							$(this).dialog('close');
						},
						
						64: function() {
							document.location.href = 'index.php?action=user&UserUID=<?php echo $this->user->getUserUID();?>&do=giveGM&Status=64';
							$(this).dialog('close');
						},
						
						80: function() {
							document.location.href = 'index.php?action=user&UserUID=<?php echo $this->user->getUserUID();?>&do=giveGM&Status=80';
							$(this).dialog('close');
						},
					
						Abbrechen: function() {
							$(this).dialog('close');
						}

					}
				});
				
				
				$("#ungmdia").dialog({
					bgiframe: true,
					autoOpen: false,
					height: 300,
					modal: true,
					buttons: {
						Ja: function() {
							document.location.href = 'index.php?action=user&UserUID=<?php echo $this->user->getUserUID();?>&do=giveGM&Status=0';
							$(this).dialog('close');
						},
					
						Nein: function() {
							$(this).dialog('close');
						}

					}

				});
				
				$("#apgdia").dialog({
					bgiframe: true,
					autoOpen: false,
					height: 300,
					modal: true,
					buttons: {
						Gutschreiben: function() {
							$("#apgdia > form").submit();
							$(this).dialog('close');
						},
					
						Abbrechen: function() {
							$(this).dialog('close');
						}

					}
				});
				
				$("#apadia").dialog({
					bgiframe: true,
					autoOpen: false,
					height: 300,
					modal: true,
					buttons: {
						Abziehen: function() {
							$("#apadia > form").submit();
							$(this).dialog('close');
						},
					
						Abbrechen: function() {
							$(this).dialog('close');
						}

					}
				});
				
				$("#pwdia").dialog({
					bgiframe: true,
					autoOpen: false,
					height: 300,
					modal: true,
					buttons: {
						�ndern: function() {
							$("#pwdia > form").submit();
							$(this).dialog('close');
						},
					
						Abbrechen: function() {
							$(this).dialog('close');
						}

					}
				});

				$('#ban').click(function() {
					$('#bandia').dialog('open');
				});
				
				$('#unban').click(function() {
					$('#unbandia').dialog('open');
				});
				
				$('#gm').click(function() {
					$('#gmdia').dialog('open');
				});
				
				$('#apg').click(function() {
					$('#apgdia').dialog('open');
				});
				
				$('#apa').click(function() {
					$('#apadia').dialog('open');
				});
				
				$('#pw').click(function() {
					$('#pwdia').dialog('open');
				});
				
				$('#ungm').click(function() {
					$('#ungmdia').dialog('open');
				});
				
				$(".tooltip").tipTip({maxWidth: "auto", edgeOffset: 10});
				

			});
			
			</script>
			
			<div id="bandia" title="Benutzer Bannen">voulez-vous '<?php echo $this->user->getName();?>' vraiment Ban?</div>
			<div id="unbandia" title="Benutzer Entbannen">voulez-vous '<?php echo $this->user->getName();?>' vraiment unban?</div>
			<div id="gmdia" title="GM Rechte geben">Quel est l'�tat que vous voulez '<?php echo $this->user->getName();?>' donner?</div>
			<div id="apgdia" title="AP geben">Combien AP que vous voulez '<?php echo $this->user->getName();?>' donner?<form action="index.php?action=user&amp;UserUID=<?php echo $this->user->getUserUID();?>&amp;do=giveAp" method="POST"><br><input type="text" name="AP" maxlength=6 size=6></form></div>
			<div id="apadia" title="AP abziehen">Combien AP que vous voulez '<?php echo $this->user->getName();?>' enlever?<form action="index.php?action=user&amp;UserUID=<?php echo $this->user->getUserUID();?>&amp;do=takeAp" method="POST"><br><input type="text" name="AP" maxlength=6 size=6></form></div>
			<div id="pwdia" title="Passwort �ndern">Comment le nouveau mot de passe par '<?php echo $this->user->getName();?>' lire?<form action="index.php?action=user&amp;UserUID=<?php echo $this->user->getUserUID();?>&amp;do=pw" method="POST"><br><input type="text" name="PW" maxlength=12 size=12></form></div>
			<div id="ungmdia" title="GM entfernen">Voulez-vous les droits de GM '<?php echo $this->user->getName();?>' vraiment retirer?</div>
			
			<?php
			
				
		}
		
	}
}

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  