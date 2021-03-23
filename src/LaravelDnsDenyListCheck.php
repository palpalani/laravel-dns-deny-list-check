<?php

declare(strict_types=1);

namespace palPalani\LaravelDnsDenyListCheck;

class LaravelDnsDenyListCheck
{
    /**
     * @param string $ip
     * @return array
     */
    public function check(string $ip): array
    {
        /**
         * Use: https://github.com/tbreuss/dns-blacklist-check
         * https://gist.github.com/tbreuss/74da96ff5f976ce770e6628badbd7dfc
         * https://gist.github.com/michaeldyrynda/cd0039d18faf9f5a1ac5
         * https://mxtoolbox.com/problem/blacklist/
         */
        /*
        $dnsblLookup = [
            'all.s5h.net',
            "b.barracudacentral.org",
            #"bl.emailbasura.org",
            #"bl.spamcannibal.org",
            "bl.spamcop.net",
            "blacklist.woody.ch",
            "bogons.cymru.com",
            "cbl.abuseat.org",
            "cdl.anti-spam.org.cn",
            "combined.abuse.ch",
            "db.wpbl.info",
            "dnsbl-1.uceprotect.net",
            "dnsbl-2.uceprotect.net",
            "dnsbl-3.uceprotect.net",
            "dnsbl.anticaptcha.net",
            "dnsbl.cyberlogic.net",
            "dnsbl.dronebl.org",
            "dnsbl.inps.de",
            "dnsbl.sorbs.net",
            "dnsbl.spfbl.net",
            "drone.abuse.ch",
            "duinv.aupads.org",
            "dul.dnsbl.sorbs.net",
            "dyna.spamrats.com",
            "dynip.rothen.com",
            "exitnodes.tor.dnsbl.sectoor.de",
            "http.dnsbl.sorbs.net",
            "ips.backscatterer.org",
            "ix.dnsbl.manitu.net",
            "korea.services.net",
            "misc.dnsbl.sorbs.net",
            "noptr.spamrats.com",
            "orvedb.aupads.org",
            "pbl.spamhaus.org",
            "proxy.bl.gweep.ca",
            "psbl.surriel.com",
            "relays.bl.gweep.ca",
            "relays.nether.net",
            "sbl.spamhaus.org",
            "short.rbl.jp",
            "singular.ttk.pte.hu",
            "smtp.dnsbl.sorbs.net",
            "socks.dnsbl.sorbs.net",
            "spam.abuse.ch",
            "spam.dnsbl.anonmails.de",
            "spam.dnsbl.sorbs.net",
            "spam.spamrats.com",
            "spambot.bls.digibase.ca",
            "spamrbl.imp.ch",
            "spamsources.fabel.dk",
            "ubl.lashback.com",
            "ubl.unsubscore.com",
            "virus.rbl.jp",
            "web.dnsbl.sorbs.net",
            "wormrbl.imp.ch",
            "xbl.spamhaus.org",
            "z.mailspike.net",
            "zen.spamhaus.org",
            "zombie.dnsbl.sorbs.net",
            //"relays.osirusoft.com"
        ];
        */

        $dnsblLookup = [
            "access.redhawk.org",
            "all.s5h.net",
            "all.spamblock.unit.liu.se",
            "b.barracudacentral.org",
            "bl.deadbeef.com",
            //"bl.emailbasura.org",
            //"bl.spamcannibal.org",
            "bl.spamcop.net",
            "black.uribl.com",
            "blackholes.five-ten-sg.com",
            "blackholes.mail-abuse.org",
            "blacklist.sci.kun.nl",
            "blacklist.woody.ch",
            "bogons.cymru.com",
            "bsb.spamlookup.net",
            "cbl.abuseat.org",
            "cbl.anti-spam.org.cn",
            "cblless.anti-spam.org.cn",
            "cblplus.anti-spam.org.cn",
            "cdl.anti-spam.org.cn",
            "combined.abuse.ch",
            "combined.njabl.org",
            "combined.rbl.msrbl.net",
            "csi.cloudmark.com",
            "db.wpbl.info",
            "dialups.mail-abuse.org",
            //"dnsbl-1.uceprotect.net",
            "dnsbl-2.uceprotect.net",
            "dnsbl-3.uceprotect.net",
            "dnsbl.abuse.ch",
            "dnsbl.anticaptcha.net",
            "dnsbl.cyberlogic.net",
            "dnsbl.dronebl.org",
            "dnsbl.inps.de",
            "dnsbl.kempt.net",
            "dnsbl.njabl.org",
            "dnsbl.sorbs.net",
            "dnsbl.spfbl.net",
            "dob.sibl.support-intelligence.net",
            "drone.abuse.ch",
            "dsn.rfc-ignorant.org",
            "duinv.aupads.org",
            "dul.blackhole.cantv.net",
            "dul.dnsbl.sorbs.net",
            "dul.ru",
            "dyna.spamrats.com",
            "dynablock.sorbs.net",
            "dyndns.rbl.jp",
            "dynip.rothen.com",
            "exitnodes.tor.dnsbl.sectoor.de",
            "forbidden.icm.edu.pl",
            "http.dnsbl.sorbs.net",
            "httpbl.abuse.ch",
            "images.rbl.msrbl.net",
            "ips.backscatterer.org",
            "ix.dnsbl.manitu.net",
            "korea.services.net",
            "ksi.dnsbl.net.au",
            //"list.dsbl.org",
            "mail.people.it",
            "misc.dnsbl.sorbs.net",
            "multi.surbl.org",
            "multi.uribl.com",
            "netblock.pedantic.org",
            "noptr.spamrats.com",
            "omrs.dnsbl.net.au",
            "opm.tornevall.org",
            "orvedb.aupads.org",
            "osrs.dnsbl.net.au",
            "pbl.spamhaus.org",
            "phishing.rbl.msrbl.net",
            "probes.dnsbl.net.au",
            "proxy.bl.gweep.ca",
            "psbl.surriel.com",
            "query.senderbase.org",
            "rbl-plus.mail-abuse.org",
            "rbl.efnetrbl.org",
            "rbl.interserver.net",
            "rbl.spamlab.com",
            "rbl.suresupport.com",
            "rdts.dnsbl.net.au",
            "relays.bl.gweep.ca",
            "relays.bl.kundenserver.de",
            "relays.mail-abuse.org",
            "relays.nether.net",
            //"relays.osirusoft.com",
            "residential.block.transip.nl",
            "ricn.dnsbl.net.au",
            "rmst.dnsbl.net.au",
            "rot.blackhole.cantv.net",
            "sbl.spamhaus.org",
            "short.rbl.jp",
            "singular.ttk.pte.hu",
            "smtp.dnsbl.sorbs.net",
            "socks.dnsbl.sorbs.net",
            "sorbs.dnsbl.net.au",
            "spam.abuse.ch",
            "spam.dnsbl.anonmails.de",
            "spam.dnsbl.sorbs.net",
            "spam.rbl.msrbl.net",
            "spam.spamrats.com",
            "spambot.bls.digibase.ca",
            "spamguard.leadmon.net",
            "spamlist.or.kr",
            "spamrbl.imp.ch",
            "spamsources.fabel.dk",
            "tor.dan.me.uk",
            "ubl.lashback.com",
            "ubl.unsubscore.com",
            "uribl.swinog.ch",
            "url.rbl.jp",
            "virbl.bit.nl",
            "virus.rbl.jp",
            "virus.rbl.msrbl.net",
            "web.dnsbl.sorbs.net",
            "wormrbl.imp.ch",
            "xbl.spamhaus.org",
            "z.mailspike.net",
            "zen.spamhaus.org",
            "zombie.dnsbl.sorbs.net",
        ];

        $result = [];

        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return [
                'success' => false,
                'message' => 'Invalid IP address',
                'data' => null,
            ];
        }

        $reverseIp = \implode('.', \array_reverse(\explode('.', $ip)));

        foreach ($dnsblLookup as $host) {
            try {
                $dnsr = $reverseIp . '.' . $host . '.';
                if (\checkdnsrr($dnsr, 'A')) {
                    $listed = true;
                } else {
                    $listed = false;
                }

                $result[] = [
                    'host' => $host,
                    'listed' => $listed,
                ];
            } catch (\Throwable $exception) {
                $result[] = [
                    'host' => $host,
                    'listed' => 'Unknown',
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return [
            'success' => true,
            'message' => '',
            'data' => $result,
        ];
    }
}
