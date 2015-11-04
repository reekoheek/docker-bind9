;
; BIND data file for <?php echo $entry['domain'] ?> interface
;
$TTL    604800
@                        IN SOA              <?php echo $entry['domain'] ?>. root.<?php echo $entry['domain'] ?>. (
                              2              ; Serial
                         604800              ; Refresh
                          86400              ; Retry
                        2419200              ; Expire
                         604800 )            ; Negative Cache TTL
;
$INCLUDE /var/lib/bind/sub.<?php echo $entry['domain'] ?>

