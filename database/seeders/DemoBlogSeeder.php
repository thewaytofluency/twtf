<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Full-length articles (plain text, paragraphs separated by blank lines — the blog view renders
 * content with nl2br, not markdown) written for English learners.
 */
class DemoBlogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        foreach ($this->posts() as [$title, $daysAgo, $content]) {
            $post = BlogPost::firstOrNew(['slug' => Str::slug($title)]);
            $post->fill([
                'title' => $title,
                'content' => trim($content),
                'author_id' => $admin->id,
            ]);
            $post->created_at = $post->created_at ?? now()->subDays($daysAgo)->setTime(9, 30);
            $post->save();
        }
    }

    /** @return array<int, array{0: string, 1: int, 2: string}> */
    private function posts(): array
    {
        return [
            ['Why You Understand English but Cannot Speak It', 75, <<<'TEXT'
Many learners tell us the same thing: "I understand almost everything, but when I have to speak, my mind goes blank." If that sounds familiar, you are not alone, and you are not failing. You are experiencing the gap between passive and active knowledge.

Passive knowledge is what you recognise when you read or listen. Active knowledge is what you can produce quickly, without help. Recognising a word takes a fraction of a second. Producing the same word, in the right form, in the right order, while someone waits for your answer, takes much more mental work. That is why speaking feels so much harder than listening.

The second reason is fear. Most of us were taught to avoid mistakes, so we stay quiet until we are sure a sentence is perfect. By then the conversation has moved on. Fluency is not about being correct all the time. It is about keeping the conversation going.

So how do you close the gap? Start by speaking to yourself. Describe what you are doing as you do it: "I am making tea. I am looking for a cup." It feels silly, and it works, because it forces your brain to build sentences without pressure.

Next, use shadowing. Pick a short video, play one sentence, pause, and repeat it out loud copying the rhythm and the intonation. Do ten minutes a day. You will train your mouth as much as your memory.

Third, record yourself. Answer a simple question for one minute: "What did you do yesterday?" Listen to the recording and write down three things you want to improve. Next week, answer the same question again. The difference will motivate you.

Finally, find a speaking partner, even one conversation a week. Do not wait until you feel ready. Nobody ever feels ready.

Remember: you do not need a bigger vocabulary to start speaking. You need more speaking. Start today with one minute.
TEXT],

            ['5 Habits of Learners Who Become Fluent', 62, <<<'TEXT'
After watching hundreds of students over the years, we noticed that the ones who reach fluency do not necessarily have more talent or more free time. They share a handful of habits. Here are five you can start this week.

1. They study a little every day. Thirty focused minutes daily beat a three-hour session once a week. Memory needs repetition over time, and short daily sessions are easier to protect from a busy schedule.

2. They learn in chunks, not single words. Instead of memorising "make" and "decision" separately, they learn "make a decision". Chunks come out of your mouth ready to use, which is exactly what you need when speaking.

3. They use English for something they enjoy. A football podcast, a cooking channel, a novel, a music playlist. When the content matters to you, you pay attention, and attention is what makes learning stick.

4. They review mistakes without shame. They keep a small notebook of errors and the corrections. Once a week they read it. Most learners make the same five or six mistakes again and again, and just knowing yours is half the cure.

5. They speak early and often. Waiting until your grammar is perfect is the slowest possible route. Speak badly first; improve later.

Pick just one of these habits and try it for two weeks. When it feels automatic, add the next one. Progress in a language is built from small routines, not heroic efforts.
TEXT],

            ['Present Perfect vs Past Simple: A Simple Way to Choose', 49, <<<'TEXT'
This is one of the most common grammar questions we receive, and Portuguese speakers have a particular reason to struggle: the Portuguese "pretérito perfeito" covers both ideas. English splits them in two.

Here is the simplest rule. Use the past simple when the time is finished and usually mentioned: "I visited Beira last year." "She called me yesterday." Use the present perfect when the time is not finished or not important, and the action still matters now: "I have visited Beira twice." "She has called me three times today."

Notice the key word: connection. The present perfect always connects the past to the present. "I have lost my keys" means the problem exists now. "I lost my keys yesterday" is just a story about the past.

Signal words help. With yesterday, last week, in 2019, two days ago, when did you, we use the past simple. With already, yet, ever, never, just, so far, this week, recently, we usually use the present perfect.

Compare these pairs:

"Did you see the film?" (a specific film, a specific time) and "Have you seen the film?" (are you familiar with it?).

"I lived in Maputo for five years." (I do not live there now) and "I have lived in Maputo for five years." (I still live there).

The second pair is the one that surprises people. The same words, a different tense, and a different reality.

A good exercise: write five true sentences about your life using the present perfect ("I have learned..."), then write the matching past simple sentence with a time ("I learned to swim in 2015"). Say them aloud. Soon the choice will feel natural.
TEXT],

            ['How to Prepare for a Job Interview in English', 36, <<<'TEXT'
An interview in a second language can be stressful, but good preparation changes everything. Interviewers rarely expect perfect English; they want clear, confident and honest communication.

Start with the question that opens almost every interview: "Tell me about yourself." Prepare a short answer of about a minute with three parts: who you are professionally, one or two strengths with a proof point, and why you want this role. Practise until it flows, but do not memorise it word for word.

Next, prepare your stories. Interviewers love behavioural questions: "Tell me about a time you solved a problem." Use the STAR method. Situation: what was the context? Task: what was your responsibility? Action: what exactly did you do? Result: what changed?

Build a small vocabulary bank. Useful verbs include: managed, organised, improved, delivered, coordinated, negotiated. Useful phrases include: "I am responsible for...", "I took the initiative to...", "As a result, we increased...".

Practise the hard questions too. "What is your biggest weakness?" requires honesty and growth: name a real weakness and explain what you are doing to improve it. "Why should we hire you?" is your chance to connect your skills to their needs.

Do not forget to prepare questions. "What does success look like in this role in the first six months?" shows ambition and curiosity.

On the day, speak a little more slowly than normal, and it is perfectly fine to say, "Could I take a moment to think about that?" or "Could you repeat the question, please?" These phrases sound professional, not weak.

Finally, practise out loud with a friend or a recording. Hearing your own answers is the fastest way to fix awkward sentences before the real thing.
TEXT],

            ['Phrasal Verbs Without the Pain', 28, <<<'TEXT'
Phrasal verbs are a verb plus a small word, such as "give up", "turn on" or "look after". They are everywhere in spoken English, and they are often where learners feel completely lost, because the meaning rarely matches the individual words.

The first piece of good news: you do not need all of them. Around fifty cover the vast majority of what you will hear every day. Start with those.

The second: learn them by topic and in sentences. Instead of a long alphabetical list, study ten verbs about work, ten about travel, ten about relationships. A sentence such as "I need to hand in my report by Friday" teaches the meaning, the preposition, and a typical situation all at once.

Third, understand the grammar. Some phrasal verbs are separable: "turn the light off" and "turn off the light" are both correct, but with a pronoun you must say "turn it off", never "turn off it". Others are inseparable: "look after the children", "look after them". Learning which is which as you meet each verb saves a lot of confusion later.

Fourth, notice the patterns. "Up" often means completion or increase: finish up, eat up, speed up. "Out" often suggests disappearing or distributing: run out, hand out, wipe out. "Off" can mean leaving or stopping: set off, switch off, call off.

Finally, use them. Write five sentences a day using new phrasal verbs about your own life. When you read or watch something in English, underline the ones you notice.

Be patient. A phrasal verb you meet in three different contexts becomes yours.
TEXT],

            ['Building Your Listening Skills: A 4-Week Plan', 17, <<<'TEXT'
Listening is the skill that most improves all the others, yet many learners practise it passively, by putting on a video and hoping something sticks. A little structure makes a huge difference. Here is a simple four-week plan, thirty minutes a day.

Week 1: listen for the main idea. Choose a short video, about five minutes, at your level. Play it once without stopping and write down, in one sentence, what it was about. Then play it again and note five details. Do not worry about unknown words.

Week 2: listen with the transcript. Play the video, pause after each sentence you did not catch, read it, and replay it. This is where you discover that you already knew the words; you just did not recognise them at speed. Pay attention to linking and reductions: "want to" becomes "wanna", "going to" becomes "gonna".

Week 3: shadow. Play a short extract, pause, and repeat out loud, imitating the speaker's rhythm, stress and intonation as closely as possible. This trains both your ear and your mouth.

Week 4: increase the challenge. Choose different accents, a faster speaker, or a podcast without a transcript. Listen first, summarise in a few sentences, and only then check.

Some tips throughout. Listen to the same material several times, because repetition is not boring, it is how your brain learns. Keep a small notebook of useful phrases you hear, not just single words. And set realistic expectations: you will not catch everything, and you do not need to.

By the end of the month you will notice something exciting: speakers who once seemed too fast will feel noticeably clearer.
TEXT],

            ['Welcome to The Way to Fluency: How Your Plan Works', 5, <<<'TEXT'
Whether you have just joined or you are thinking about it, here is a quick guide to how your learning plan works.

Every student starts on the free level. This gives you access to our public lessons, the beginner study guides and a selection of videos, so you can find out whether our way of teaching suits you.

When you are ready to go further, you can choose a paid plan from the Subscription page. Each plan opens a higher access level: Basic unlocks more beginner and intermediate material, Standard adds the full lesson library and expert tips, and Premium gives you everything, including advanced content and exclusive resources.

To subscribe, choose a plan and send us a proof of payment. We accept M-Pesa, e-Mola and bank transfer. A team member will review your request, normally within one working day, and your plan becomes active as soon as it is approved. You will see the status of your request on the Subscription page at any time.

Once your plan is active, everything within your level is unlocked: videos, documents and the study guide. Your subscription lasts thirty days, and you can request a renewal at any time.

Remember to explore the blog and join the conversation in the comments. Questions from students often turn into our next lessons.

If anything is unclear, reach us through any of the social links at the bottom of the home page or by WhatsApp. We are here to help you every step of the way.

Welcome aboard, and enjoy the journey.
TEXT],
        ];
    }
}
