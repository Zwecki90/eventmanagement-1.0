<?php
	if (!isset($_SESSION['user']))
	{
		return printf("<div class=\"event-management-body-content-text-container\">Es ist ein Fehler aufgetreten: Sie sind nicht angemeldet!</div>");
	}
		
	$user = unserialize($_SESSION['user']);
	if ($user->access < 2)
	{
		return printf("<div class=\"event-management-body-content-text-container\">Es ist ein Fehler aufgetreten: Sie haben keinen Zugriff auf diesen Teil der Anwendung!</div>");
	}
?>
<div class="event-management-body-content-manage">
	<h1 class="event-management-body-content-manage-header">Veranstaltungsverwaltung</h1>
	<form action="#" class="event-management-body-content-manage-form">
		<h2>Neue Veranstaltung erstellen</h2>
		<table>
			<tr>
				<td>
					<label for="eventManagementBodyContentManageFormTitle">Titel:</label>
				</td>
				<td colspan="5">
					<input id="eventManagementBodyContentManageFormTitle" type="text" name="title" maxlength="100" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="eventManagementBodyContentManageFormDescription">Beschreibung:</label>
				</td>
				<td colspan="5">
					<textarea id="eventManagementBodyContentManageFormDescription" name="description" maxlength="1000"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<label for="eventManagementBodyContentManageFormDateTime">Datum/Uhrzeit:</label>
				</td>
				<td colspan="5">
					<input id="eventManagementBodyContentManageFormDateTime" class="event-management-date-time" value="__.__.____ __:__" type="text" name="datetime" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="eventManagementBodyContentManageFormMaxCount">Max. Teilnehmerzahl:</label>
				</td>
				<td colspan="5">
					<input id="eventManagementBodyContentManageFormMaxCount" class="event-management-number-field" type="number" value="1" name="maxcount" min="1" />
				</td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="id" value="" />
				</td>
				<td colspan="5">
					<input type="submit" value="Erstellen" /><input type="reset" value="Zurücksetzen" />
				</td>
			</tr>
		</table>
	</form>
	<div class="event-management-body-content-manage-event-list">
		<?php
			$stmt = $db->prepare('SELECT e.id AS eventid, e.title, e.description, e.date, e.max_count, COUNT(p.id) AS p_count FROM event e LEFT JOIN participation p ON e.id = p.ptr2eventid WHERE ptr2owner = :userid AND e.date >= NOW() GROUP BY e.title ORDER BY e.date;');
			$stmt->bindParam(':userid', $user->id, PDO::PARAM_INT);
			$stmt->execute();
			
			if ($stmt->rowCount() < 1)
			{
				printf('Es gibt im Moment keine Veranstaltung von Ihnen, die nicht bereits vorüber ist.');
			}
			else
			{
				$index = 1;
				while($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$eventid = $row['eventid'];
					$p_count = $row['p_count'];
					$max_count = $row['max_count'];
					
					$substmt = $db->prepare('SELECT a.email FROM access a LEFT JOIN participation p ON p.ptr2accessid = a.id WHERE p.ptr2eventid = :eventid;');
					$substmt->bindParam(':eventid', $eventid, PDO::PARAM_INT);
					$substmt->execute();
					$emails = $substmt->fetchAll(PDO::FETCH_COLUMN, 0);
					$eventdate = date('d.m.Y H:i', strtotime($row['date']));
					$title = $row['title'];
					$desc = $row['description'];
					
					printf('				<form action="#" class="event-management-body-list-event-form">
							<input type="hidden" name="eventid" value="%d" />
							<input type="hidden" name="title" value="%s" />
							<input type="hidden" name="desc" value="%s" />
							<input type="hidden" name="date" value="%s" />
							<input type="hidden" name="maxcount" value="%d" />
							<input type="hidden" name="pcount" value="%d" />
							<table class="event-management-body-list-event">
								<tr>
									<td rowspan="2" class="event-management-body-list-event-index-cell">%d</td>
									<th colspan="9">%s</th>
									<th>%d/%d</th>
									<th colspan="5">%s</th>
									<th colspan="3" style="text-align:right;"><input type="button" value="Bearbeiten" class="event-management-body-list-event-form-edit-button" /></th>
									<th colspan="3" style="text-align:right;"><input type="button" value="Löschen" class="event-management-body-list-event-form-delete-button" /></th>',
									$eventid,
									$title,
									$desc,
									$eventdate,
									$max_count,
									$p_count,
									$index,
									$title,
									$p_count,
									$max_count,
									$eventdate);

					printf('
								</tr>
								<tr>
									<td colspan="21">%s</td>
								</tr>
								<tr>
									<th colspan="3" style="background-color:white;color: black;">Teilnehmer:</th>
									<td colspan="19">%s</td>
								</tr>
							</table>
						</form>',
							$desc,
							count($emails) < 1 ? 'bisher keine Teilnehmer' : implode(', ', $emails));

					$index++;
				}
			}
		?>
	</div>
</div>