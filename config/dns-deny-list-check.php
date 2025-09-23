<?php

/**
 * DNSBL/RBL Configuration - Broader MXToolbox-Style Set (2025)
 *
 * This list includes a broader set of commonly referenced DNSBL services similar
 * to those surfaced by MXToolbox. Some entries may be more aggressive, regional,
 * or require registration for high-volume usage. Tune tiers/timeouts per needs.
 *
 * Notes:
 * - Tier 1/2: Generally reputable and widely used
 * - Tier 3: Supplementary or aggressive/regional lists; use with caution
 * - Some providers (e.g., Spamhaus/CBL) may throttle or require registration
 */

return [
    'servers' => [

        // ==================== TIER 1: CRITICAL - VERIFIED WORKING ====================
        // These are the most trusted and widely-used DNSBLs by major email providers

        // Previously removed services can be enabled below if desired (now added under Tier 2)

        ['name' => 'SpamCop Blocking List', 'host' => 'bl.spamcop.net', 'tier' => 1, 'priority' => 'critical'],
        ['name' => 'Barracuda Reputation Block List', 'host' => 'b.barracudacentral.org', 'tier' => 1, 'priority' => 'critical'],
        ['name' => 'UCEPROTECT Level 1', 'host' => 'dnsbl-1.uceprotect.net', 'tier' => 1, 'priority' => 'critical'],

        // ==================== TIER 2: IMPORTANT - VERIFIED WORKING ====================
        // Specialized and regional authority DNSBLs with good reputation

        ['name' => 'DroneB Anti-Abuse', 'host' => 'dnsbl.dronebl.org', 'tier' => 2, 'priority' => 'important'],
        ['name' => 'Backscatterer IPS', 'host' => 'ips.backscatterer.org', 'tier' => 2, 'priority' => 'important'],
        ['name' => 'Blocklist.de', 'host' => 'bl.blocklist.de', 'tier' => 2, 'priority' => 'important'],
        ['name' => 'Mailspike Z', 'host' => 'z.mailspike.net', 'tier' => 2, 'priority' => 'important'],
        // Additional widely-referenced DNSBLs (some may require registration for heavy use)
        ['name' => 'Spamhaus ZEN', 'host' => 'zen.spamhaus.org', 'tier' => 2, 'priority' => 'important'],
        ['name' => 'Composite Blocking List (CBL)', 'host' => 'cbl.abuseat.org', 'tier' => 2, 'priority' => 'important'],
        ['name' => 'Mailspike BL', 'host' => 'bl.mailspike.net', 'tier' => 2, 'priority' => 'important'],

        // ==================== TIER 3: SUPPLEMENTARY - VERIFIED WORKING ====================
        // Additional verified lists for comprehensive coverage

        ['name' => 'Policy Block List (PSBL)', 'host' => 'psbl.surriel.com', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'WPBL Write Protect Block List', 'host' => 'db.wpbl.info', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Spamsources Fabel', 'host' => 'spamsources.fabel.dk', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Korea Services', 'host' => 'korea.services.net', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Cymru Bogons', 'host' => 'bogons.cymru.com', 'tier' => 3, 'priority' => 'supplementary'],
        // Broader MXToolbox-style supplementary set
        ['name' => 'EFnet RBL', 'host' => 'rbl.efnetrbl.org', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'Tornevall DNSBL', 'host' => 'dnsbl.tornevall.org', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'SPFBL DNSBL', 'host' => 'dnsbl.spfbl.net', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'InterServer RBL', 'host' => 'rbl.interserver.net', 'tier' => 3, 'priority' => 'supplementary'],
        ['name' => 'GBUdb DNSBL', 'host' => 'dnsbl.gbudb.net', 'tier' => 3, 'priority' => 'supplementary'],

        // ==================== DEFUNCT/NON-FUNCTIONAL SERVICES ====================
        // The following services have been tested and found non-functional:

        // SORBS - SHUT DOWN JUNE 2024 by Proofpoint
        // ❌ ['name' => 'SORBS DNSBL', 'host' => 'dnsbl.sorbs.net'] - PERMANENTLY SHUT DOWN
        // ❌ ['name' => 'SORBS DUL', 'host' => 'dul.dnsbl.sorbs.net'] - PERMANENTLY SHUT DOWN
        // ❌ ['name' => 'SORBS Zombie', 'host' => 'zombie.dnsbl.sorbs.net'] - PERMANENTLY SHUT DOWN
        // ❌ ['name' => 'SORBS HTTP', 'host' => 'http.dnsbl.sorbs.net'] - PERMANENTLY SHUT DOWN
        // ❌ ['name' => 'SORBS SMTP', 'host' => 'smtp.dnsbl.sorbs.net'] - PERMANENTLY SHUT DOWN
        // ❌ ['name' => 'SORBS SOCKS', 'host' => 'socks.dnsbl.sorbs.net'] - PERMANENTLY SHUT DOWN

        // SPAMRATS - DNS RESOLUTION FAILED
        // ❌ ['name' => 'Spam Rats', 'host' => 'spam.spamrats.com'] - NOT RESOLVING
        // ❌ ['name' => 'SpamRats Dynamic', 'host' => 'dyna.spamrats.com'] - NOT RESOLVING
        // ❌ ['name' => 'SpamRats NoPtr', 'host' => 'noptr.spamrats.com'] - NOT RESOLVING

        // SPAMHAUS/CBL - included above; may require registration for heavy use

        // OTHER NON-FUNCTIONAL
        // ❌ ['name' => 'Abuse.ch Combined', 'host' => 'combined.abuse.ch'] - NOT RESOLVING
        // ❌ ['name' => 'Manitu IX', 'host' => 'ix.dnsbl.manitu.net'] - NOT RESOLVING

        // INTENTIONALLY EXCLUDED - TOO AGGRESSIVE/PROBLEMATIC:
        // ❌ ['name' => 'UCEPROTECT L2', 'host' => 'dnsbl-2.uceprotect.net'] - TOO AGGRESSIVE
        // ❌ ['name' => 'UCEPROTECT L3', 'host' => 'dnsbl-3.uceprotect.net'] - TOO AGGRESSIVE
        // ❌ ['name' => 'DSBL', 'host' => 'list.dsbl.org'] - DISCONTINUED
        // ❌ ['name' => 'Osirusoft', 'host' => 'relays.osirusoft.com'] - DEFUNCT
    ],
];
