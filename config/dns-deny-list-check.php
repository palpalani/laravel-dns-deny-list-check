<?php

/**
 * DNSBL/RBL Configuration - Curated for 2024-2025 Email Deliverability
 *
 * This configuration includes only actively maintained, widely-adopted DNSBL services
 * that are actually used by major email providers (Gmail, Outlook, Yahoo, etc.)
 *
 * References:
 * - https://github.com/tbreuss/dns-blacklist-check
 * - https://gist.github.com/tbreuss/74da96ff5f976ce770e6628badbd7dfc
 * - https://gist.github.com/michaeldyrynda/cd0039d18faf9f5a1ac5
 * - https://mxtoolbox.com/problem/blacklist/
 */

return [
    'servers' => [
        // TIER 1: CRITICAL - Widely Used by Major Providers
        ['name' => 'Spamhaus ZEN', 'host' => 'zen.spamhaus.org', 'tier' => 1, 'priority' => 'critical'],
        ['name' => 'SpamCop', 'host' => 'bl.spamcop.net', 'tier' => 1, 'priority' => 'critical'],
        ['name' => 'Barracuda Central', 'host' => 'b.barracudacentral.org', 'tier' => 1, 'priority' => 'critical'],

        // TIER 2: IMPORTANT - Regional/Specialized Authority
        ['name' => 'SORBS DNSBL', 'host' => 'dnsbl.sorbs.net', 'tier' => 2, 'priority' => 'important'],
        ['name' => 'UCEPROTECT Level 1', 'host' => 'dnsbl-1.uceprotect.net', 'tier' => 2, 'priority' => 'important'],
        ['name' => 'Composite Blocking List', 'host' => 'cbl.abuseat.org', 'tier' => 2, 'priority' => 'important'],

        // TIER 3: SUPPLEMENTARY - Specialized Use Cases
        ['name' => 'DroneB', 'host' => 'dnsbl.dronebl.org', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Spam Rats', 'host' => 'spam.spamrats.com', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Backscatterer', 'host' => 'ips.backscatterer.org', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Blocklist.de', 'host' => 'bl.blocklist.de', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Mailspike Z', 'host' => 'z.mailspike.net', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Policy Block List', 'host' => 'psbl.surriel.com', 'tier' => 3, 'priority' => 'supplementary'],

        // ADDITIONAL MAINTAINED LISTS (Lower Priority)
        ['name' => 'SpamRats Dynamic', 'host' => 'dyna.spamrats.com', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'SpamRats NoPtr', 'host' => 'noptr.spamrats.com', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'WPBL', 'host' => 'db.wpbl.info', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'Abuse.ch Combined', 'host' => 'combined.abuse.ch', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'Manitu IX', 'host' => 'ix.dnsbl.manitu.net', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'Spamsources Fabel', 'host' => 'spamsources.fabel.dk', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'SORBS DUL', 'host' => 'dul.dnsbl.sorbs.net', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'SORBS Zombie', 'host' => 'zombie.dnsbl.sorbs.net', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'SORBS HTTP', 'host' => 'http.dnsbl.sorbs.net', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'SORBS SMTP', 'host' => 'smtp.dnsbl.sorbs.net', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'SORBS SOCKS', 'host' => 'socks.dnsbl.sorbs.net', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'Korea Services', 'host' => 'korea.services.net', 'tier' => 4, 'priority' => 'additional'],
        ['name' => 'Cymru Bogons', 'host' => 'bogons.cymru.com', 'tier' => 4, 'priority' => 'additional'],

        // DEPRECATED/PROBLEMATIC LISTS (COMMENTED OUT)
        // The following lists are either defunct, too aggressive, or problematic:
        // ['name' => 'DSBL', 'host' => 'list.dsbl.org'],  // DISCONTINUED
        // ['name' => 'Osirusoft', 'host' => 'relays.osirusoft.com'],  // DEFUNCT
        // ['name' => 'UCEPROTECT L2', 'host' => 'dnsbl-2.uceprotect.net'],  // TOO AGGRESSIVE
        // ['name' => 'UCEPROTECT L3', 'host' => 'dnsbl-3.uceprotect.net'],  // TOO AGGRESSIVE
        // ['name' => 'Individual Spamhaus Lists', 'host' => 'sbl.spamhaus.org'],  // USE ZEN INSTEAD
    ],
];
