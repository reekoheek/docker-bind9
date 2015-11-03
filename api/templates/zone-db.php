;
; BIND data file for <?php echo $model['domain'] ?> interface
;
$TTL    604800
@                        IN SOA              <?php echo $model['domain'] ?>. root.<?php echo $model['domain'] ?>. (
                              2              ; Serial
                         604800              ; Refresh
                          86400              ; Retry
                        2419200              ; Expire
                         604800 )            ; Negative Cache TTL
<?php foreach ($config['ns'] as $ns => $ip) : ?>
@                        IN NS               <?php echo $ns ?>.<?php echo $model['domain'] ?>.
<?php endforeach ?>
;
@                        IN A                <?php echo $model['ip'] ?>

;
<?php foreach ($config['ns'] as $ns => $ip) : ?>
<?php echo $ns ?>                      IN A                <?php echo $ip ?>

<?php endforeach ?>
;
<?php foreach($model['records'] as $record): ?>
<?php if ($record['type'] === 'MX'): ?>
<?php echo sprintf('%s IN %s %s %s', str_pad($record['name'], 24), str_pad($record['type'], 16), $record['priority'], $record['value']) ?>
<?php else: ?>
<?php echo sprintf('%s IN %s %s', str_pad($record['name'], 24), str_pad($record['type'], 16), $record['value']) ?>
<?php endif ?>

<?php endforeach ?>