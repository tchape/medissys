<div id="plainContent">
	<div class="breadcrumb">
	<p> <span class="design"> ACCEUIL </span> ::: medical software administration. Nous sommes le <?php echo strftime("%d %B %Y"); //date("d/m/Y").', '.date("H:i"); ?> </p>
	<?php //TODO: Convertir date en Français ?>
	<hr/>
	</div>
</div>

<div class="dashboard">
	<p class="board"> DASHBOARD - MES RDV</p>
	<table> 
		<thead>
			<tr>
				<th>N° DOSSIER</th>
				<th>NOM</th>
				<th>PRENOM</th>
				<th>DATE</th>
				<th>HEURE</th>
				<th>STATUT</th>
			</tr>
		</thead>
		<tbody> 
		<?php foreach ($row as $key => $value) {

		?>
			<tr>
				<!--td> <strong> <?php //echo $value->num_dossier; ?> </strong> </td-->
				<td> <strong> <?php echo anchor('Consultation/consulter/'.$value->num_dossier,$value->num_dossier,'class="linkTable"'); ?> </strong> </td>
				<td> <?php echo mb_strtoupper($value->nom); ?> </td>
				<td> <?php echo ucwords($value->prenom);?> </td>
				<td> <?php echo $value->date; ?> </td>
				<td> <?php echo $value->heure; ?> </td>
				<td> <?php echo $value->type; ?> </td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	</div>
</div>
<?php echo css_url('table'); ?>