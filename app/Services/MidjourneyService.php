<?php

namespace App\Services;

use GuzzleHttp\Client;

class MidjourneyService
{
    /**
     * Generate an image using Midjourney based on the provided prompt.
     *
     * @param string $prompt
     * @return string
     */

    protected $MIDAUTHUSERTOKEN;
    protected $channel_id;

    public function __construct()
    {
        $this->MIDAUTHUSERTOKEN = env('MIDAUTHUSERTOKEN');
        $this->channel_id = env('channel_id');
    }

    public function imagine(string $prompt): string
    {
        $jobIdFile = md5($prompt);
        
        // Check if the job already exists
        if (file_exists('./jobs/' . $jobIdFile)) {
            $jobId = file_get_contents('./jobs/' . $jobIdFile);
            return 'https://cdn.midjourney.com/' . $jobId . '/0_1.png'; // or 0_2.png, 0_3.png, 0_4.png
        }

        // Create a new Guzzle HTTP client
        $client = new Client([
            'proxy' => 'socks5://127.0.0.1:1080' // Add SOCKS5 proxy
        ]);
        // Prepare headers
        $headers = [
            'channelId' => $this->channel_id,
            'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:125.0) Gecko/20100101 Firefox/125.0',
            'Accept' => '*/*',
            'Accept-Language' => 'fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Referer' => 'https://www.midjourney.com/imagine',
            'Content-Type' => 'application/json',
            'X-CSRF-Protection' => '1',
            'Origin' => 'https://www.midjourney.com',
            'Alt-Used' => 'www.midjourney.com',
            'Connection' => 'keep-alive',
            'Cookie' => 'AMP_MKTG_437c42b22c=JTdCJTdE; _ga=GA1.1.874097738.1727007675; _gcl_au=1.1.960560718.1727007675; __stripe_mid=397f471f-625c-4412-9971-44231c1e96cb6b9ff3; cf_clearance=66BW0xzgwr.Q08JXM3WYOYVcRMIVVripyA1LzCYlRmk-1727282303-1.2.1.1-M.M9Gd_ZHHyq6m7w2lJ.QvGTYbVlZYQyt_zjyiy_EmWMrJnVQx2QD9cXKTJej_PxkNC3rrVkYcJC0uQoksTAX6p3F9oW3gfhvd6gSDB3DcYD6rUXrRDQ8wnXSlbhU36c2fBAxpco_q9MDugAJPloKnc7RxS8TfjVHVCCQjtL2Y28NMT8aAzFE.bTECv.1K64WDAyuKeuAR._RDuyOmtOgIm3XqFG0Hx77Kj.wgKrW2b33YA3BLbZKuPrBbTNgq29EmLxzdz9EwldZRMCMRI4MM7uAaUFTVgEUI5zzPmDlRattDlNwEHpnfrkwgR69ZIAv7V6kyqJ91RsLe.WplIY4C4tLu6mizIeDbKulRx0jw.UEAbQsFeNBPbJjaYEseq3; _ga_Q0DQ5L7K0D=GS1.1.1727282308.3.1.1727282309.0.0.0; __stripe_sid=e538181a-126c-4969-9aed-5aa4c3ebb7a05b12fc; __Host-Midjourney.AuthUserToken=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoiZmlyZWJhc2UiLCJpZFRva2VuIjoiZXlKaGJHY2lPaUpTVXpJMU5pSXNJbXRwWkNJNkltVXdNMkUyT0RnM1lXVTNaak5rTVRBeU56TmpOalJpTURVM1pUWTFNekUxTVdVeU9UQmlOeklpTENKMGVYQWlPaUpLVjFRaWZRLmV5SnVZVzFsSWpvaVpISmZkMlZpSWl3aWNHbGpkSFZ5WlNJNkltaDBkSEJ6T2k4dmJHZ3pMbWR2YjJkc1pYVnpaWEpqYjI1MFpXNTBMbU52YlM5aEwwRkRaemh2WTBvd2QweEJaMVZCUkU0dE1FZEZlVGxSUVVSa2JURmphemhxTTB0eFQzVXpXamxQWVdRMVduUnhZbXR6UTJOd1JWSnpRbWM5Y3prMkxXTWlMQ0p0YVdScWIzVnlibVY1WDJsa0lqb2lPRGM1T0daa09EZ3RNRGhpTkMwME1EZG1MV0ZoTWpFdFltRXlNRFJoWWpWaE5HUXpJaXdpYVhOeklqb2lhSFIwY0hNNkx5OXpaV04xY21WMGIydGxiaTVuYjI5bmJHVXVZMjl0TDJGMWRHaHFiM1Z5Ym1WNUlpd2lZWFZrSWpvaVlYVjBhR3B2ZFhKdVpYa2lMQ0poZFhSb1gzUnBiV1VpT2pFM01qY3dNRGMyT1Rnc0luVnpaWEpmYVdRaU9pSnNRMWRPUVVaT1NGbE9XVVZ0WlVjeWJuVTVkMlkyYVhaNFRESXpJaXdpYzNWaUlqb2liRU5YVGtGR1RraFpUbGxGYldWSE1tNTFPWGRtTm1sMmVFd3lNeUlzSW1saGRDSTZNVGN5TnpJNE16SXpPU3dpWlhod0lqb3hOekkzTWpnMk9ETTVMQ0psYldGcGJDSTZJbXRvYjNOb1pHVnNMbTVsZEVCbmJXRnBiQzVqYjIwaUxDSmxiV0ZwYkY5MlpYSnBabWxsWkNJNmRISjFaU3dpWm1seVpXSmhjMlVpT25zaWFXUmxiblJwZEdsbGN5STZleUpuYjI5bmJHVXVZMjl0SWpwYklqRXdPRFkwTlRVek56a3dOVFE0TWpBNE5qZ3pOU0pkTENKa2FYTmpiM0prTG1OdmJTSTZXeUl4TURjM05qY3pNelUxTlRZM05qazNPVFF4SWwwc0ltVnRZV2xzSWpwYkltdG9iM05vWkdWc0xtNWxkRUJuYldGcGJDNWpiMjBpWFgwc0luTnBaMjVmYVc1ZmNISnZkbWxrWlhJaU9pSm5iMjluYkdVdVkyOXRJbjE5LlR0NkxMYXNCSTJQSi1DUXVrYnNIenROTXhvRHY5TEpGdEFURVhQNENkS3B3Rm42TS1jZDZmZU92aDgzVWUwMjMycG9TanNCU2Jnd1lFNVRhQy1rcXpQTWt3bTFwMEozLVp2RDhnS1dHWlNKRnJBbVV5RTEtRjZWSGV4bmFhR19mSVNrQjBwRjNRdDNJQ2ZDbTJFRU9vemxiamdfSlRNRkdlNjRhLXdDUzl3eXZlckpGT0N0QWlqblpuUy1PTkhMd1dHSi14MUdSeVpnOWhad2EwbjI1MC12NzZnRW1IbTJ5dm1WRXBKcjdoaHo2ODBaeDZuYVVNaHpoSWd3X0x1NTlfRDRxUm8yUkFxaGU2bU5mcnI4bHRINU9OeUZUbHczbVd6Yk1tcWJMNERKMEJfbldVSUFtTFprSUEzR3Awa1BoQ2c3cklVWEk2X3dod05pUk1RZUQ4ZyIsInJlZnJlc2hUb2tlbiI6IkFNZi12QndDQVFvODZPS1czaC1qR01XdkM4RUZUMWtONi1iaU85M2NmdzZUNHk2T1NOdGpvZjc4OVJIYi1Gam9rVWh1UVFwcEJzNlJSYTFfUzhOWXYtSm9XMzBrU3NBNndlcmFEcDJicmV4NTBTZDJPclFVUDgzTnY5YzdLblFGdU9hbFh6ajg1REV3dDVtaFgyd2N3eHJFWncwaU4wWmx6bjBzWU5LT3BQOWoxX0JkQVNwR05DMk1FRTJqaWoxVU9ReHFMQl9VM1I4akRPME5nTnBwbTIwNDgzdVRKRnRTQl81bWJKNmxmV1FocVQ4T3pHMHI1QzdKQVlVM0xhd0FfcWFTdnZSZ1ltMlFfTUNJTm9yZGNCNzJ1bUYwU1JReUREUE9ZV1JzcVJycnd0LVV2VVA4MEh5YlR0UlRNT0ptdmZZU0dNYTBpRnNOTU5Zc0NFV0syY0hjNVg2dm9sV2d3SFVaRUhZMzRwYWp6NE55aUhpZFpIM2RqbmFBUlk4aHZ2b0RDbWpSZWxyeDBRTHZ6cTF0SUY0Vm5Bc0tsMkdBNjdHMmJud2RfdGJCT3ZjQjR5bnhLWFVCTnRZZllsRGNLVUdNbFNRczJjbUUiLCJpYXQiOjE3MjcyODMyMzl9.VVKemafQDhpZEUFvBlFRsRKU7t_imG29k2X_iUP6vQ4; __cf_bm=HNKwBin4THysVmuTVcwhuLrBj8HU9ciukKNmxLaefVA-1727283239-1.0.1.1-Q0hrSSGBdXV2U9KjbA7vNVBf98aEwUkDdFhAe0kOvkuzNYI17VU84adUDmB3zkgtOH2G_uRBHjo4DD1TlnTjCQ; AMP_437c42b22c=JTdCJTIyZGV2aWNlSWQlMjIlM0ElMjJjYWJhZTJkZi1hYWRmLTRlMWMtODhjMS1mZTIyNzlmYmZkZTUlMjIlMkMlMjJ1c2VySWQlMjIlM0ElMjI4Nzk4ZmQ4OC0wOGI0LTQwN2YtYWEyMS1iYTIwNGFiNWE0ZDMlMjIlMkMlMjJzZXNzaW9uSWQlMjIlM0ExNzI3MjgyMzA5MDMyJTJDJTIyb3B0T3V0JTIyJTNBZmFsc2UlMkMlMjJsYXN0RXZlbnRUaW1lJTIyJTNBMTcyNzI4MzI0NzE0OSUyQyUyMmxhc3RFdmVudElkJTIyJTNBMTQ4JTdE; _dd_s=logs=1&id=b046d60b-6c65-41d0-8e77-8a8e5fa529b4&created=1727282308309&expire=1727284156438',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'TE' => 'trailers'
        ];

        // Prepare body
        $body = json_encode([
            'prompt' => $prompt,
            'prompts' => [$prompt],
            'parameters' => new \stdClass(),
            'flags' => [
                'mode' => 'fast',
                'private' => false
            ],
            'jobType' => 'imagine',
            'id' => null,
            'index' => null,
            'metadata' => [
                'imagePrompts' => 0,
                'imageReferences' => 0,
                'characterReferences' => 0,
                'autoPrompt' => false
            ]
        ]);

        // Send the request to Midjourney API
        $response = $client->request('POST', 'https://www.midjourney.com/api/app/submit-jobs', [
            'headers' => $headers,
            'body' => $body
        ]);

        // Handle the response
        $res = $response->getBody();
        if ($res) {
            $res = json_decode($res, true);
            $jobId = $res['success'][0]['job_id'];
            file_put_contents('./jobs/' . $jobIdFile, $jobId);
            return 'https://cdn.midjourney.com/' . $jobId . '/0_1.png';
        }

        throw new \Exception('Error generating image');
    }
}