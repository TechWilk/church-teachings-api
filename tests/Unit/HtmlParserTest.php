<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use TechWilk\Church\Teachings\IngestFeed\Parser\HtmlParser;

class HtmlParserTest extends TestCase
{
    public function testParseItems()
    {
        $html = file_get_contents(__DIR__ . '/../data/scrape.html');
        $parser = new HtmlParser();

        $mappings = [
            'item' => 'main > section > div > .listing',
            'title' => '.left > a > h3',
            'verses' => '.left > p',
            'date' => '.left > small > time',
            'speaker' => '.left > small > a',
            'series' => '.left > a > p',
        ];

        $parsedItems = $parser->parseFeed($html, $mappings);

        $expectedItems = [
            [
                'title' => 'Money Changes Everything',
                'verses' => 'Luke 12:13-21',
                'date' => '15/03/2020',
                'speaker' => 'Sam Durdant-Hollamby',
                'series' => 'Keep Yourselves from idols',
            ],
            [
                'title' => '1 Sipping salt water',
                'verses' => 'Isaiah 44 6-23',
                'date' => '08/03/2020',
                'speaker' => 'John Kirkland',
                'series' => 'Keep Yourselves from idols',
            ],
            [
                'title' => 'World Church',
                'verses' => 'Psalm 72',
                'date' => '23/02/2020',
                'speaker' => 'Bill Holt',
                'series' => 'Standalone Sermon',
            ],
            [
                'title' => 'Christians as citizens',
                'verses' => 'Luke 10 25-39',
                'date' => '16/02/2020',
                'speaker' => 'John Kirkland',
                'series' => 'Living Christianity',
            ],
            [
                'title' => 'Life Gender Marriage and Family',
                'verses' => '',
                'date' => '09/02/2020',
                'speaker' => 'John Benson',
                'series' => 'Living Christianity',
            ],
            [
                'title' => 'Healing the Paralytic Man',
                'verses' => '',
                'date' => '02/02/2020',
                'speaker' => '',
                'series' => 'Standalone Sermon',
     
            ],
            [
                'title' => 'Christians at work',
                'verses' => 'Genesis 1:26-2:20',
                'date' => '26/01/2020',
                'speaker' => 'Jeff Turnbull',
                'series' => 'Living Christianity',
            ],
            [
                'title' => 'Christians and the World',
                'verses' => '2 Corinthians 5:14-21',
                'date' => '19/01/2020',
                'speaker' => 'Sam Durdant-Hollamby',
                'series' => 'Living Christianity',
            ],
            [
                'title' => 'Concern for Society',
                'verses' => 'Matthew 5:13-16',
                'date' => '12/01/2020',
                'speaker' => 'John Kirkland',
                'series' => 'Living Christianity',
            ],
            [
                'title' => 'The Prophet\'s Christmas Present',
                'verses' => '',
                'date' => '22/12/2019',
                'speaker' => '',
                'series' => 'Standalone Sermon',
            ],
        ];

        $this->assertEquals($expectedItems, $parsedItems);
    }

    public function testParseItemsAgain()
    {
        $html = file_get_contents(__DIR__ . '/../data/scrape2.html');
        $parser = new HtmlParser();

        $mappings = [
            'item' => '.media_recording',
            'title' => '.media_recording_title a',
            'verses' => '.media_recording_details li:nth-of-type(4) a',
            'date' => '.media_recording_details li:nth-of-type(2)',
            'speaker' => '.media_recording_speaker',
            'series' => '.left > a > p',
        ];

        $parsedItems = $parser->parseFeed($html, $mappings);

        $expectedItems = [
            [
                'title' => 'The Beloved - A year in John\'s Gospel - 7pm service',
                'verses' => 'John 4:1-42',
                'date' => 'Recorded: 15/03/2020',
                'speaker' => 'George Briggs',
                'series' => '',
            ],
            [
                'title' => 'All eyes on Jesus: Killed and Raised? - 10.30am service',
                'verses' => 'Luke 9:18-27',
                'date' => 'Recorded: 15/03/2020',
                'speaker' => 'Chris Mullen',
                'series' => '',
            ],
            [
                'title' => 'All eyes on Jesus: Killed and Raised? - 9am service',
                'verses' => 'Luke 9:18-27',
                'date' => 'Recorded: 15/03/2020',
                'speaker' => 'Dave Sharps',
                'series' => '',
            ],
            [
                'title' => 'All Eyes on Jesus : Mission Break Out! - 9am service',
                'verses' => 'Acts 12:5-17',
                'date' => 'Recorded: 09/03/2020',
                'speaker' => 'Kent Anderson/ Stephen Bell',
                'series' => '',
            ],
            [
                'title' => 'All eyes on Jesus : Mission Break Out! - 10.30am service',
                'verses' => 'Acts 12:5-17',
                'date' => 'Recorded: 09/03/2020',
                'speaker' => 'Kent Anderson/Stephen Bell',
                'series' => '',
            ],
            [
                'title' => 'The Beloved - A year in John\'s Gospel: John the Baptist exalts Jesus - 7pm service',
                'verses' => 'John 3:22-36',
                'date' => 'Recorded: 09/03/2020',
                'speaker' => 'Stephen Bell',
                'series' => '',
            ],
            [
                'title' => 'All eyes on Jesus: An Unwelcome Guest? 10.30am',
                'verses' => 'Luke 7:36-50',
                'date' => 'Recorded: 01/03/2020',
                'speaker' => 'George Briggs',
                'series' => '',

            ],
            [
                'title' => 'All eyes on Jesus: Why Him? 10.30am',
                'verses' => 'Luke 19:1-10',
                'date' => 'Recorded: 23/02/2020',
                'speaker' => 'George Briggs',
                'series' => '',
            ],
            [
                'title' => 'The Beloved - A year in John\'s Gospel: Unravelling Nicodemus 7.00pm',
                'verses' => 'John 3:1-21',
                'date' => 'Recorded: 23/02/2020',
                'speaker' => 'Chris Mullen',
                'series' => '',
            ],
            [
                'title' => 'All eyes on Jesus: Why Him? 9.00am',
                'verses' => 'Luke 19:1-10',
                'date' => 'Recorded: 23/02/2020',
                'speaker' => 'George Briggs',
                'series' => '',
            ],
        ];

        $this->assertEquals($expectedItems, $parsedItems);
    }
}