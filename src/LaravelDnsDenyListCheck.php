<?php

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
         * https://mxtoolbox.com/problem/blacklist/
         */
        $dnsblLookup = [
            'all.s5h.net',
//            "b.barracudacentral.org",
//            #"bl.emailbasura.org",
//            #"bl.spamcannibal.org",
//            "bl.spamcop.net",
//            "blacklist.woody.ch",
//            "bogons.cymru.com",
//            "cbl.abuseat.org",
//            "cdl.anti-spam.org.cn",
//            "combined.abuse.ch",
//            "db.wpbl.info",
//            "dnsbl-1.uceprotect.net",
//            "dnsbl-2.uceprotect.net",
//            "dnsbl-3.uceprotect.net",
//            "dnsbl.anticaptcha.net",
//            "dnsbl.cyberlogic.net",
//            "dnsbl.dronebl.org",
//            "dnsbl.inps.de",
//            "dnsbl.sorbs.net",
//            "dnsbl.spfbl.net",
//            "drone.abuse.ch",
//            "duinv.aupads.org",
//            "dul.dnsbl.sorbs.net",
//            "dyna.spamrats.com",
//            "dynip.rothen.com",
//            "exitnodes.tor.dnsbl.sectoor.de",
//            "http.dnsbl.sorbs.net",
//            "ips.backscatterer.org",
//            "ix.dnsbl.manitu.net",
//            "korea.services.net",
//            "misc.dnsbl.sorbs.net",
//            "noptr.spamrats.com",
//            "orvedb.aupads.org",
//            "pbl.spamhaus.org",
//            "proxy.bl.gweep.ca",
//            "psbl.surriel.com",
//            "relays.bl.gweep.ca",
//            "relays.nether.net",
//            "sbl.spamhaus.org",
//            "short.rbl.jp",
//            "singular.ttk.pte.hu",
//            "smtp.dnsbl.sorbs.net",
//            "socks.dnsbl.sorbs.net",
//            "spam.abuse.ch",
//            "spam.dnsbl.anonmails.de",
//            "spam.dnsbl.sorbs.net",
//            "spam.spamrats.com",
//            "spambot.bls.digibase.ca",
//            "spamrbl.imp.ch",
//            "spamsources.fabel.dk",
//            "ubl.lashback.com",
//            "ubl.unsubscore.com",
//            "virus.rbl.jp",
//            "web.dnsbl.sorbs.net",
//            "wormrbl.imp.ch",
//            "xbl.spamhaus.org",
//            "z.mailspike.net",
//            "zen.spamhaus.org",
//            "zombie.dnsbl.sorbs.net"
            /*
            "dnsbl-1.uceprotect.net",
            "dnsbl-2.uceprotect.net",
            "dnsbl-3.uceprotect.net",
            "dnsbl.dronebl.org",
            "dnsbl.sorbs.net",
            "zen.spamhaus.org",
            "bl.spamcop.net",
            "list.dsbl.org",
            "sbl.spamhaus.org",
            "xbl.spamhaus.org",
            "relays.osirusoft.com"
            */
        ];
        $result = [];
        //$ip = '69.44.44.33';

        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return [
                'success' => false,
                'message' => 'Invalid IP address',
                'data' => null,
            ];
        }

        $reverseIp = \implode('.', \array_reverse(\explode('.', $ip)));

        foreach ($dnsblLookup as $host) {
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
        }

        return [
            'success' => true,
            'message' => '',
            'data' => $result,
        ];
    }
}
