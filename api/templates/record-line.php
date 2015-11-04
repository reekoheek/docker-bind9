<?php if ($entry['type'] === 'MX'): ?>
<?php echo sprintf('%s IN %s %s %s', str_pad($entry['key'], 24), str_pad($entry['type'], 16), $entry['priority'], $entry['value']) ?>
<?php else: ?>
<?php echo sprintf('%s IN %s %s', str_pad($entry['key'], 24), str_pad($entry['type'], 16), $entry['value']) ?>
<?php endif ?>
