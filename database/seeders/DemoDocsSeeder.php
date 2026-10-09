<?php

namespace Database\Seeders;

use App\Enums\CourseLevel;
use App\Enums\UserRole;
use App\Models\Doc;
use App\Models\User;
use Database\Seeders\Support\SimplePdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Study documents with real, usable content, generated as actual PDFs on the private disk so
 * downloads (and the access check in DocController@download) can be tested end to end.
 */
class DemoDocsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        foreach ($this->documents() as $i => $doc) {
            $slug = Str::slug($doc['title']);
            $path = "docs/demo-{$slug}.pdf";

            $pdf = new SimplePdf($doc['title'], $doc['subtitle']);
            foreach ($doc['sections'] as $heading => $body) {
                $pdf->heading($heading);
                is_array($body) ? $pdf->bullets($body) : $pdf->paragraph($body);
            }
            $contents = $pdf->render();

            Storage::disk('local')->put($path, $contents);

            $model = Doc::firstOrNew(['file_path' => $path]);
            $model->fill([
                'title' => $doc['title'],
                'description' => $doc['description'],
                'original_filename' => "{$slug}.pdf",
                'file_size' => strlen($contents),
                'course_level' => CourseLevel::from($doc['level']),
                'required_access_level' => $doc['access'],
                'created_by' => $admin->id,
            ]);
            $model->sort_order = $model->sort_order ?: $i + 1;
            $model->created_at = $model->created_at ?? now()->subDays(90 - $i * 8);
            $model->save();
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function documents(): array
    {
        return [
            [
                'title' => 'English Tenses Cheat Sheet',
                'subtitle' => 'One page per idea: form, use and a clear example for each tense',
                'description' => 'The twelve main tenses with form, use and examples. Print it and keep it next to your notebook.',
                'level' => 'beginner', 'access' => 0,
                'sections' => [
                    'Present tenses' => [
                        'Present simple (I work): habits, facts and timetables. "She works at a bank." "The train leaves at 8."',
                        'Present continuous (I am working): actions happening now or temporary situations. "I am studying right now."',
                        'Present perfect (I have worked): past actions with a link to now, or life experience. "I have visited Maputo twice."',
                        'Present perfect continuous (I have been working): actions that started in the past and continue, or just stopped with visible results. "I have been waiting for an hour."',
                    ],
                    'Past tenses' => [
                        'Past simple (I worked): finished actions at a definite time. "We met in 2019."',
                        'Past continuous (I was working): an action in progress at a past moment, often interrupted. "I was cooking when she called."',
                        'Past perfect (I had worked): the earlier of two past actions. "When I arrived, the class had started."',
                        'Past perfect continuous (I had been working): the duration before a past moment. "She was tired because she had been studying all night."',
                    ],
                    'Future forms' => [
                        'Will (I will work): spontaneous decisions and predictions. "I think it will rain."',
                        'Be going to (I am going to work): plans and evidence-based predictions. "I am going to start a course in March."',
                        'Present continuous for fixed arrangements: "I am meeting my tutor at 5."',
                        'Future perfect (I will have worked): finished before a future moment. "By June I will have finished the book."',
                    ],
                    'Quick check' => 'Look for the signal word: "yesterday, ago, last" points to past simple. "Already, yet, ever, since, for" often points to present perfect. "Now, at the moment, currently" points to present continuous. "Always, usually, every day" points to present simple.',
                ],
            ],
            [
                'title' => 'Articles A An The: Rules and Practice',
                'subtitle' => 'When to use a, an, the, or nothing at all',
                'description' => 'A clear guide to English articles, with the exceptions that most often confuse Portuguese speakers.',
                'level' => 'beginner', 'access' => 0,
                'sections' => [
                    'A and an' => 'Use a or an with singular, countable nouns when you mention something for the first time or when it is not specific. Use a before a consonant sound (a book, a university) and an before a vowel sound (an apple, an hour). It is the sound that matters, not the spelling.',
                    'The' => [
                        'Something already mentioned: "I bought a book. The book is excellent."',
                        'Something unique: the sun, the president, the internet.',
                        'Something specific from context: "Close the door, please."',
                        'Superlatives and ordinals: the best, the first.',
                        'Rivers, oceans and some countries: the Zambezi, the Indian Ocean, the United States.',
                    ],
                    'No article' => [
                        'Plural and uncountable nouns in general: "Teachers need patience." "Water is essential."',
                        'Most names of countries, cities and people: Mozambique, Beira, Maria.',
                        'Meals, languages and sports: "We have lunch at one." "I speak English." "She plays football."',
                        'Fixed expressions: go to school, go to bed, at home, by bus.',
                    ],
                    'Typical mistakes' => [
                        'Wrong: "I like the music." (in general) Right: "I like music."',
                        'Wrong: "She is teacher." Right: "She is a teacher."',
                        'Wrong: "I have a good news." Right: "I have good news." (news is uncountable)',
                        'Wrong: "He plays the football." Right: "He plays football."',
                    ],
                    'Practice' => [
                        '1. I saw ___ film yesterday. ___ film was very long. (a / The)',
                        '2. She works as ___ engineer. (an)',
                        '3. ___ life is full of surprises. (no article)',
                        '4. We stayed at ___ hotel near ___ airport. (a / the)',
                    ],
                ],
            ],
            [
                'title' => '100 Everyday English Phrases',
                'subtitle' => 'Useful sentences for daily life, grouped by situation',
                'description' => 'Ready-to-use phrases for greetings, shopping, travel, work and emergencies. Learn five a day.',
                'level' => 'beginner', 'access' => 0,
                'sections' => [
                    'Greetings and small talk' => [
                        'How are you doing? / I am doing well, thanks. And you?',
                        'Nice to meet you. / Pleased to meet you too.',
                        'What do you do? / I am a student. / I work in sales.',
                        'Could you say that again, please? / Could you speak a bit more slowly?',
                        'What does ___ mean? / How do you spell that?',
                    ],
                    'Shopping and restaurants' => [
                        'How much is this? / Do you have this in a larger size?',
                        'Can I pay by card? / Do you accept mobile money?',
                        'I would like a table for two, please.',
                        'Could I see the menu? / I will have the chicken, please.',
                        'Could we have the bill, please?',
                    ],
                    'Getting around' => [
                        'Excuse me, where is the nearest bus stop?',
                        'How long does it take to get there?',
                        'Does this bus go to the city centre?',
                        'I think I am lost. Could you help me?',
                    ],
                    'At work or school' => [
                        'Could you send me the details by email?',
                        'I am sorry I am late. / Sorry for the delay.',
                        'Can we reschedule the meeting for Thursday?',
                        'I did not understand the last part. Could you explain it again?',
                    ],
                    'Emergencies' => [
                        'I need help! / Please call an ambulance.',
                        'I have lost my phone. / My bag has been stolen.',
                        'I do not feel well. I need a doctor.',
                    ],
                ],
            ],
            [
                'title' => 'Conditionals Guide: Zero to Third',
                'subtitle' => 'Real and unreal situations, with a mixed conditional bonus',
                'description' => 'All the English conditionals explained with timelines, patterns and exercises.',
                'level' => 'intermediate', 'access' => 1,
                'sections' => [
                    'Zero conditional: facts' => 'Pattern: if + present simple, present simple. "If you heat ice, it melts." Use it for general truths and rules.',
                    'First conditional: real future possibility' => 'Pattern: if + present simple, will + base verb. "If it rains tomorrow, we will stay home." Never use will in the if-clause.',
                    'Second conditional: unreal or unlikely present' => 'Pattern: if + past simple, would + base verb. "If I had more time, I would learn French." With be, many speakers use were for all persons: "If I were you, I would apply."',
                    'Third conditional: unreal past' => 'Pattern: if + past perfect, would have + past participle. "If she had studied, she would have passed." It expresses regret or imagines a different past.',
                    'Mixed conditional' => 'Past condition with a present result: "If I had taken that job, I would be living in Johannesburg now." Present condition with a past result: "If he were more careful, he would not have lost the keys."',
                    'Useful alternatives to if' => [
                        'Unless = if not: "Unless you hurry, you will miss the bus."',
                        'As long as / provided that: "You can borrow it as long as you return it."',
                        'Suppose / supposing: "Suppose you won the lottery, what would you do?"',
                        'Otherwise: "Leave now, otherwise you will be late."',
                    ],
                    'Exercises' => [
                        '1. If I (be) rich, I (buy) a house by the sea.',
                        '2. If they (leave) earlier, they (not miss) the flight.',
                        '3. She (call) you if she (have) time.',
                        '4. If you (mix) red and blue, you (get) purple.',
                    ],
                ],
            ],
            [
                'title' => 'Common Mistakes Made by Portuguese Speakers',
                'subtitle' => 'False friends, word order and prepositions that cause trouble',
                'description' => 'The errors we hear most from Portuguese-speaking learners, with the corrections and the reason behind each.',
                'level' => 'intermediate', 'access' => 1,
                'sections' => [
                    'False friends' => [
                        'Actually does not mean "atualmente". It means "in fact". For "currently" say currently or at the moment.',
                        'Pretend does not mean "pretender". It means "fingir". To intend, say intend or plan.',
                        'Push is not "puxar". Push means "empurrar"; pull means "puxar".',
                        'Realize means "to understand suddenly", not "realizar". To carry out, say accomplish or carry out.',
                        'Parents are "pais". Relatives are "parentes".',
                        'Library is "biblioteca". A bookshop is "livraria".',
                    ],
                    'Grammar patterns' => [
                        'Do not drop the subject: "Is raining" should be "It is raining". English needs a subject in every sentence.',
                        'Say "I am 25 years old" and "I am cold", not "I have 25 years" or "I have cold".',
                        'Adjectives come before nouns and never take a plural: "two big houses", not "two houses bigs".',
                        'Use the -ing form after prepositions: "interested in learning", "good at cooking".',
                        'Make questions with do/does/did: "Where do you live?", not "Where you live?".',
                    ],
                    'Prepositions' => [
                        'Depend on (not depend of). Listen to (not listen the music).',
                        'Arrive in a city or country, at a small place: "arrive in Maputo", "arrive at the station".',
                        'Married to (not married with). Different from (not different of).',
                        'On Monday, in July, at 5 pm, in 2024.',
                    ],
                    'Pronunciation traps' => [
                        'Do not add a vowel before s clusters: say "school", not "eschool".',
                        'Final -ed has three sounds: /t/ (worked), /d/ (played), /id/ (wanted).',
                        'The th sounds: put your tongue between your teeth for think and this.',
                    ],
                ],
            ],
            [
                'title' => 'Phrasal Verbs Workbook',
                'subtitle' => '40 phrasal verbs grouped by theme, with example sentences',
                'description' => 'Forty high-frequency phrasal verbs organised by topic, each with a clear meaning and example.',
                'level' => 'intermediate', 'access' => 2,
                'sections' => [
                    'How to study phrasal verbs' => 'Learn them in sentences, not in lists. Group them by topic, and review five a day. Remember that many have more than one meaning: always check the context.',
                    'Work and study' => [
                        'carry out: do or complete. "The team carried out a survey."',
                        'fill in: complete a form. "Please fill in this form."',
                        'hand in: submit. "Hand in your essay on Friday."',
                        'look into: investigate. "We will look into the problem."',
                        'set up: create or arrange. "She set up her own company."',
                        'take on: accept a responsibility. "He took on extra work."',
                        'catch up: reach the same level. "I need to catch up on the lessons I missed."',
                        'point out: draw attention to. "She pointed out an error."',
                    ],
                    'Everyday life' => [
                        'wake up: stop sleeping. get up: leave your bed.',
                        'run out of: have none left. "We have run out of milk."',
                        'pick up: collect. "Can you pick me up at six?"',
                        'drop off: leave someone or something somewhere. "I will drop the kids off at school."',
                        'turn down: refuse, or reduce volume. "He turned down the offer."',
                        'put off: postpone. "They put off the meeting."',
                        'throw away: discard. "Do not throw away the receipt."',
                        'get along with: have a good relationship with. "I get along with my colleagues."',
                    ],
                    'Feelings and relationships' => [
                        'cheer up: become happier. "Cheer up, it is not that bad."',
                        'calm down: relax. "Calm down and tell me what happened."',
                        'fall out with: stop being friends. "She fell out with her sister."',
                        'make up: become friends again. "They argued, but made up."',
                        'look up to: admire. "I look up to my teacher."',
                        'let down: disappoint. "I do not want to let you down."',
                    ],
                    'Travel' => [
                        'check in / check out: register on arrival / leave a hotel.',
                        'set off: start a journey. "We set off at dawn."',
                        'get on / get off: board / leave a bus, train or plane.',
                        'take off: leave the ground. "The flight takes off at nine."',
                    ],
                ],
            ],
            [
                'title' => 'Business Email Phrases',
                'subtitle' => 'Open, request, follow up and close professionally',
                'description' => 'Polite, professional phrases for every part of a work email, with formal and neutral variants.',
                'level' => 'intermediate', 'access' => 2,
                'sections' => [
                    'Opening' => [
                        'Formal: Dear Mr Silva, / Dear Ms Machel,',
                        'Neutral: Hello Ana, / Hi Carlos,',
                        'Thank you for your email. / Thank you for getting back to me.',
                        'I am writing to enquire about ... / I am writing regarding ...',
                    ],
                    'Making requests' => [
                        'Could you please send me ...?',
                        'I would be grateful if you could ...',
                        'Would it be possible to ...?',
                        'Please let me know if you need anything else.',
                    ],
                    'Giving news and information' => [
                        'I am pleased to inform you that ...',
                        'Unfortunately, we are unable to ...',
                        'Please find attached the report / invoice / document.',
                        'As discussed on the phone, ...',
                    ],
                    'Following up' => [
                        'I am following up on my previous email.',
                        'I just wanted to check whether you had a chance to look at ...',
                        'Could you let me know the status of ...?',
                    ],
                    'Closing' => [
                        'I look forward to hearing from you.',
                        'Thank you for your time and assistance.',
                        'Formal: Yours sincerely, / Kind regards,',
                        'Neutral: Best wishes, / Many thanks,',
                    ],
                    'Common mistakes' => [
                        'Do not write "Best regards," and then continue the email. A closing phrase ends the message.',
                        'Avoid contractions in very formal emails (do not, cannot).',
                        'Use "I would like to" instead of "I want to".',
                        'Check names and titles: if unsure, use the full name.',
                    ],
                ],
            ],
            [
                'title' => 'Word Stress and Silent Letters',
                'subtitle' => 'Say it the way native speakers do',
                'description' => 'Where to put the stress in common words, plus the silent letters that spelling hides.',
                'level' => 'intermediate', 'access' => 2,
                'sections' => [
                    'Why stress matters' => 'English is stress-timed: stressed syllables are longer and clearer, and unstressed syllables are shortened. Wrong stress can make a word hard to recognise even when every sound is correct.',
                    'Noun or verb?' => [
                        'Many two-syllable words are stressed on the first syllable as nouns and on the second as verbs: REcord (noun) / reCORD (verb); PREsent / preSENT; PROject / proJECT.',
                    ],
                    'Common patterns' => [
                        'Words ending in -tion, -sion, -ic, -ical: stress the syllable before the ending. eduCAtion, ecoNOmic.',
                        'Words ending in -ity, -graphy: stress the syllable before. ciVILity, phoTOgraphy.',
                        'Compound nouns: stress the first part. a GREENhouse (a place for plants) vs a green HOUSE (a house that is green).',
                    ],
                    'Silent letters' => [
                        'Silent k: know, knife, knee. Silent w: write, wrong, answer.',
                        'Silent b: climb, doubt, debt. Silent t: listen, castle, often (often both).',
                        'Silent h: hour, honest, heir. Silent gh: night, though, daughter.',
                        'Silent l: walk, talk, half, calm. Silent p: psychology, receipt.',
                    ],
                    'Practice' => [
                        'Say each word aloud and mark the stress: comfortable, vegetable, Wednesday, photograph, photographer.',
                        'Record yourself reading a short paragraph, then compare with a native recording.',
                    ],
                ],
            ],
            [
                'title' => 'Linking Words and Connectors for Fluent Writing',
                'subtitle' => 'Structure arguments and tell clearer stories',
                'description' => 'The connectors that separate good writing from great writing, grouped by function.',
                'level' => 'advanced', 'access' => 3,
                'sections' => [
                    'Adding information' => [
                        'Moreover, furthermore, in addition, what is more.',
                        '"The course is affordable. Moreover, it is flexible."',
                    ],
                    'Contrasting' => [
                        'However, nevertheless, nonetheless, on the other hand, whereas, while.',
                        'Although / even though + clause: "Although it was late, we kept working."',
                        'Despite / in spite of + noun or -ing: "Despite the rain, the match went ahead."',
                    ],
                    'Cause and result' => [
                        'Because of / due to / owing to + noun. Since / as / because + clause.',
                        'Therefore, consequently, as a result, thus, hence.',
                        'So ... that / such ... that: "It was such a good lesson that nobody wanted to leave."',
                    ],
                    'Sequencing' => [
                        'Firstly, secondly, finally. To begin with, subsequently, meanwhile, eventually.',
                        'Prior to, following, in the meantime.',
                    ],
                    'Giving examples and emphasis' => [
                        'For instance, such as, namely, in particular, notably.',
                        'Indeed, in fact, above all, needless to say.',
                    ],
                    'Concluding' => [
                        'In conclusion, to sum up, overall, all things considered, on balance.',
                    ],
                    'Tips' => [
                        'Do not start every sentence with a connector. Vary the structure.',
                        'Punctuation: a connector at the start is followed by a comma. "However, the results were mixed."',
                        'Two main clauses joined by however need a semicolon: "It was cheap; however, it broke."',
                    ],
                ],
            ],
            [
                'title' => 'Idioms and Collocations for Fluent Speakers',
                'subtitle' => 'Natural expressions that make your English sound native',
                'description' => 'Fifty idioms and collocations organised by theme, with meaning and example.',
                'level' => 'advanced', 'access' => 3,
                'sections' => [
                    'Work and success' => [
                        'hit the ground running: start something with energy. "She hit the ground running in her new job."',
                        'go the extra mile: make more effort than expected.',
                        'back to square one: start again after a failure.',
                        'get the ball rolling: begin a process.',
                        'on the same page: in agreement.',
                    ],
                    'Time and effort' => [
                        'at the eleventh hour: at the last possible moment.',
                        'burn the midnight oil: work late into the night.',
                        'bite the bullet: do something difficult you have been avoiding.',
                        'a piece of cake: very easy.',
                    ],
                    'Opinions and communication' => [
                        'beat around the bush: avoid the main point.',
                        'cut to the chase: get to the point.',
                        'read between the lines: understand the hidden meaning.',
                        'speak your mind: say what you really think.',
                    ],
                    'Strong collocations' => [
                        'make progress, make an effort, make a decision.',
                        'do homework, do research, do business.',
                        'take a risk, take advantage of, take responsibility.',
                        'heavy rain, strong coffee, deeply grateful, utterly ridiculous, bitterly cold.',
                    ],
                    'How to use idioms' => [
                        'Learn them in a full sentence and use them within a week or you will forget them.',
                        'Do not overuse idioms in formal writing. One or two well-chosen ones are enough.',
                        'Check the register: some idioms are informal or slang.',
                    ],
                ],
            ],
        ];
    }
}
