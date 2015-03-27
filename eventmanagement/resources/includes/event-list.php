<div class="event-management-body-content-list">
	<?php
		if (empty($user) && isset($_SESSION['user']) && unserialize($_SESSION['user'])->access > 0)
		{
			$user = unserialize($_SESSION['user']);
		}
		
		$stmt = $db->prepare('SELECT id FROM event LIMIT 1');
		$stmt->execute();

		if ($stmt->rowCount())
		{
			$stmt = $db->prepare('SELECT e.id AS eventid, e.title, e.description, e.date, e.max_count, COUNT(p.id) AS p_count FROM event e LEFT JOIN participation p ON e.id = p.ptr2eventid WHERE e.date >= NOW() GROUP BY e.title ORDER BY e.date;');
			$stmt->execute();
			
			if ($stmt->rowCount() < 1)
			{
				printf('Zur Zeit stehen keine Veranstaltungen an.');
			}
			else
			{
				$index = 1;
				while($row = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$eventid = $row['eventid'];
					$p_count = $row['p_count'];
					$max_count = $row['max_count'];
					
					$substmt = $db->prepare('SELECT ptr2accessid FROM participation WHERE ptr2eventid = :eventid;');
					$substmt->bindParam(':eventid', $eventid, PDO::PARAM_INT);
					$substmt->execute();
					$ids = $substmt->fetchAll(PDO::FETCH_COLUMN, 0);
					
					$eventdate = date('d.m.Y H:i', strtotime($row['date']));
					$datetime = DateTime::createFromFormat('d.m.Y H:i', $eventdate);
					$now = mktime();
					
					printf('				<table class="event-management-body-list-event">
							<tr>
								<td rowspan="2" class="event-management-body-list-event-index-cell">%d</td>
								<th colspan="9">%s</th>
								<th>%d/%d</th>
								<th colspan="5">%s</th>
								<td colspan="6" rowspan="2" class="event-management-body-list-event-form-cell">
									<form action="#" ',
									$index,
									$row['title'],
									$p_count,
									$max_count,
									$eventdate);
					
					if (empty($user))
					{
						printf('>
										<span>Sie sind nicht eingeloggt.</span>');
					}
					elseif ($datetime->getTimeStamp() /*der Timestamp des Veranstaltungsdatums*/ - mktime() /*der aktuelle Timestamp*/ < 0)
					{
						printf('>
										<span>Die Veranstaltung ist bereits vorrüber.</span>');
					}
					elseif (is_array($ids) && in_array($user->id, $ids))
					{
						printf('class="event-management-body-list-event-cancel-participation">
										<input type="submit" value="Teilnahme absagen" />');
					}
					elseif ($p_count < $max_count)
					{
						printf('class="event-management-body-list-event-participate">
										<input type="submit" value="teilnehmen" />');
					}
					else
					{
						printf('>
										<span>Die maximale Teilnehmerzahl ist bereits erreicht.</span>');
					}

					printf('
										<input type="hidden" name="eventid" value="%d" />
									</form>
								</td>
							</tr>
							<tr>
								<td colspan="15">%s</td>
							</tr>
						</table>
					',
						$eventid,
						$row['description']);

					$index++;
				}
			}
		}
		else
		{
			printf('Zur Zeit stehen keine Veranstaltungen an.');
		}
	?>
</div>
