<?php
/**
 * TEMPORARY: Exact module content from Word docs - CORRECTED.
 * Visit: https://www.wellmeproject.com/wp-admin/?wellme_fix_content=1
 * DELETE THIS FILE AFTER RUNNING ONCE.
 */
add_action('admin_init', function() {
    if (!isset($_GET['wellme_fix_content']) || !current_user_can('manage_options') || !function_exists('update_field')) return;

    $modules = get_posts([
        'post_type' => 'wellme_module', 'posts_per_page' => -1,
        'post_status' => 'publish', 'orderby' => 'meta_value_num',
        'meta_key' => 'module_number', 'order' => 'ASC',
    ]);

    $log = [];
    foreach ($modules as $m) {
        $n = (int)get_field('module_number', $m->ID);
        $log[] = "Module $n (ID:{$m->ID})";

        // ==========================================
        // MODULE 1 - GESEME
        // ==========================================
        if ($n === 1) {
            update_field('module_subtitle', 'Positive Psychology for Youth Trainers', $m->ID);
            update_field('module_description', 'Positive Psychology looks at what helps people feel well, grow, and thrive. It focuses on wellbeing, personal strengths, and the conditions that support positive development in individuals and communities.', $m->ID);
            update_field('module_color', '#27AE60', $m->ID);
            update_field('module_motto', '"Happiness is not something ready-made. It comes from your own actions." — Dalai Lama', $m->ID);

            update_field('module_introduction',
                '<p><strong>What this module is about:</strong></p>'
                .'<p>Positive Psychology looks at what helps people feel well, grow, and thrive. It focuses on wellbeing, personal strengths, and the conditions that support positive development in individuals and communities.</p>'
                .'<p>In this module, you will explore several key ideas that can support your youth work:</p>'
                .'<ul>'
                .'<li><strong>Wellbeing (PERMA):</strong> A model that explains wellbeing through five elements: positive emotions, engagement, relationships, meaning, and accomplishment.</li>'
                .'<li><strong>Strength-based development:</strong> Focusing on young people\'s strengths, talents, and potential, rather than only on their challenges.</li>'
                .'<li><strong>Resilience:</strong> Supporting young people in learning how to cope with difficulties and grow from their experiences.</li>'
                .'<li><strong>Intrinsic motivation:</strong> Encouraging participation by giving young people autonomy, helping them build skills, and strengthening connections with others.</li>'
                .'<li><strong>Flow and positive emotions:</strong> Creating engaging activities that spark curiosity, creativity, and active participation.</li>'
                .'</ul>'
                .'<p><strong>Why these concepts are important in local/rural youth work contexts:</strong></p>'
                .'<p>Supporting young people\'s wellbeing is an important part of your work as a youth trainer. It helps young people deal with challenges, build confidence, and find their own path in life.</p>'
                .'<p>In local or rural communities, young people may sometimes experience: fewer opportunities and resources for personal growth; social isolation and limited spaces where they can connect with others; less access to educational or well-being support services.</p>'
                .'<p>Positive Psychology offers you simple and practical ways to support young people by: focusing on their strengths and potential; encouraging resilience, motivation, and self-confidence; creating opportunities for connection, belonging, and positive relationships.</p>'
                .'<p>By using these approaches in your youth work, you can help young people recognize their abilities, build emotional resources, and develop a more positive outlook for their future.</p>'
                .'<p><strong>Connection to WellMe project:</strong></p>'
                .'<p>The WellMe project focuses on supporting young people\'s wellbeing by helping them feel more confident, connected, and supported in their communities. To make this possible, the project provides youth trainers with practical ideas and activities that can easily be used in working with young people.</p>'
                .'<p>By giving you useful tools and methods, WellMe aims to help you create spaces where young people can build confidence, develop resilience, and form positive relationships.</p>'
                .'<p>This module contributes to these goals by introducing simple Positive Psychology concepts and activities that you can apply with youth groups. Through this module, you will be able to: discover practical wellbeing activities to use with young people; focus more on young people\'s strengths and potential; create positive and supportive group environments; help develop local spaces where young people feel included, valued, and empowered.</p>'
            , $m->ID);

            update_field('module_conclusion',
                '<p>Small everyday moments often pass unnoticed, yet they can have a powerful impact on our wellbeing. By learning to pause, reflect on positive experiences, and express appreciation, young people can strengthen their emotional awareness, relationships, and sense of belonging.</p>'
                .'<p><em>"Happiness is not something ready-made. It comes from your own actions."</em> — Dalai Lama</p>'
            , $m->ID);

            update_field('module_learning_outcomes', [
                [
                    'outcome_title' => 'Noticing Positive Moments',
                    'outcome_detail' => '<p>Through this activity, you will explore how helping young people notice positive moments and express appreciation can support wellbeing and strengthen relationships. The exercise also shows how simple reflection practices can help young people become more aware of positive experiences and build stronger connections within a group.</p>'
                    .'<p><strong>Knowledge:</strong> Understanding how practices such as gratitude and noticing positive moments can support young people\'s wellbeing and emotional resilience.</p>'
                    .'<p><strong>Skills:</strong> Learning how to guide reflection activities, encourage appreciation, and facilitate meaningful conversations within youth groups.</p>'
                    .'<p><strong>Attitudes:</strong> Becoming more aware of everyday positive moments and recognizing the importance of creating supportive and respectful spaces where young people feel valued and connected.</p>',
                ],
                [
                    'outcome_title' => 'Connection to Module Goals',
                    'outcome_detail' => '<p>This activity shows you how ideas from Positive Psychology can be turned into simple and practical tools for your youth work. By guiding young people to reflect on positive experiences and share appreciation, you can help them understand how positive emotions and social connections support wellbeing.</p>'
                    .'<p>Through this activity, you can: introduce simple wellbeing practices in your work with young people; apply strength-based approaches that focus on young people\'s potential; support the development of optimism, resilience, and positive relationships; encourage reflection and emotional awareness within youth groups; create safe, supportive, and inclusive environments where young people feel valued. The activity can also help you facilitate open conversations and strengthen group connections when working with youth.</p>',
                ],
            ], $m->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title' => 'Introduce the Activity (2-3 min)',
                    'step_content' => '<p>Explain that the exercise invites participants to pause and reflect on positive moments from their daily life. Sometimes we move quickly from one activity to another and forget to notice the small things that make us feel good or supported.</p>'
                    .'<p>In this activity, participants will: recall a recent positive moment from their life; reflect on what made that moment meaningful; recognize people or situations that contributed to it; share appreciation and positive experiences within the group.</p>'
                    .'<p>The goal is to show how noticing and sharing positive experiences can strengthen wellbeing, connection, and a supportive group atmosphere.</p>',
                    'step_hotspot_x' => 15, 'step_hotspot_y' => 30,
                ],
                [
                    'step_title' => 'Recall (10 min)',
                    'step_content' => '<p>Invite participants to think of a recent positive moment from their daily life. It can be something simple, such as a kind interaction, a peaceful moment, a small success, or an enjoyable activity. Ask them to write short notes about:</p>'
                    .'<ul><li>What happened?</li><li>Where were they?</li><li>What did they see, hear, or felt?</li><li>Why was the moment meaningful?</li></ul>',
                    'step_hotspot_x' => 30, 'step_hotspot_y' => 25,
                ],
                [
                    'step_title' => 'Reflect (5 min)',
                    'step_content' => '<p>Guide participants through a short reflection. Ask them to close their eyes, breathe slowly, and mentally return to that moment. Encourage them to notice additional details, emotions, and sensations they may have missed the first time. Afterward, invite them to add one more detail to their notes.</p>',
                    'step_hotspot_x' => 45, 'step_hotspot_y' => 35,
                ],
                [
                    'step_title' => 'Recognize (12 min)',
                    'step_content' => '<p>Divide participants into pairs. Each person shares their positive moment with their partner, speaking slowly and clearly. Encourage partners to listen actively and, after the sharing, discuss:</p>'
                    .'<ul><li>What made the moment meaningful?</li><li>What emotions were present?</li><li>What conditions or people helped make that moment possible?</li></ul>',
                    'step_hotspot_x' => 60, 'step_hotspot_y' => 28,
                ],
                [
                    'step_title' => 'Share: Gratitude Ladder, Wall & Practice Transfer (30-35 min)',
                    'step_content' => '<p><strong>Gratitude Ladder (15 min):</strong> Give each participant three cards or small sheets of paper. Card 1: one thing they are grateful for and why. Card 2: who contributed directly or indirectly to that positive experience. Card 3: one small action they can take to pass that positive feeling forward to someone else.</p>'
                    .'<p><strong>Gratitude Wall (10 min):</strong> Invite participants to place Card 1 on a shared wall, flipchart, or board. Ask the group to walk around, read the messages, and reflect on the variety of positive experiences in the room. If appropriate, participants can add short appreciation notes or encouraging comments to others\' cards.</p>'
                    .'<p><strong>Practice Transfer (10 min):</strong> Invite participants to reflect on how the ideas explored in this activity could be applied in their daily life. Ask: When in their daily life they could take a moment to notice and reflect on positive experiences. How could they express appreciation or gratitude to people who support them. One simple action they could try during the next week.</p>',
                    'step_hotspot_x' => 75, 'step_hotspot_y' => 40,
                ],
                [
                    'step_title' => 'Closing Reflection (3-5 min)',
                    'step_content' => '<p>End the activity by inviting participants to think of one relationship they would like to nurture in the coming week and one small appreciative action they could take.</p>',
                    'step_hotspot_x' => 90, 'step_hotspot_y' => 32,
                ],
            ], $m->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'How did reflecting on a positive moment influence your mood or perspective during the activity?'],
                ['reflection_question' => 'Did you notice any new details or feelings when you took time to think more deeply about that moment?'],
                ['reflection_question' => 'Why do you think it is sometimes difficult to notice positive moments in our daily lives?'],
                ['reflection_question' => 'How can sharing positive experiences or appreciation influence relationships and group atmosphere?'],
                ['reflection_question' => 'How does this activity relate to the ideas of Positive Psychology, such as focusing on strengths and positive emotions?'],
                ['reflection_question' => 'In what ways can practices like gratitude and reflection support wellbeing and resilience in everyday life?'],
                ['reflection_question' => 'How could activities like this help create more positive and supportive learning environments for young people?'],
                ['reflection_question' => 'What is one simple action you could try this week to notice positive moments more often or express appreciation to someone in your life?'],
            ], $m->ID);

            update_field('module_table_of_contents', "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions", $m->ID);
            $log[] = '  -> OK';
        }

        // ==========================================
        // MODULE 2 - EUROPEAN PROGRESS
        // ==========================================
        if ($n === 2) {
            update_field('module_subtitle', 'Resilience and Adaptability', $m->ID);
            update_field('module_description', 'The cultivation of resilience, growth mindset, and adaptability represents a foundational triad in contemporary youth development and wellbeing education. In this module we will delve deeper into the above concepts and see practices and approaches that contribute to the development of these skills.', $m->ID);
            update_field('module_color', '#E74C3C', $m->ID);
            update_field('module_motto', '"It is not the thing or the situation that disturbs, but the opinion about the thing or the situation", Epictetus', $m->ID);

            update_field('module_introduction',
                '<p><strong>What this module is about:</strong></p>'
                .'<p>The cultivation of resilience, growth mindset, and adaptability represents a foundational triad in contemporary youth development and wellbeing education. In an era characterized by social uncertainty, technological transformation, and evolving labor markets, young individuals must be equipped with the psychological resources and cognitive flexibility necessary to navigate change effectively. In this module we will delve deeper into the above concepts and see practices and approaches that contribute to the development of these skills.</p>'
                .'<ul>'
                .'<li><strong>Resilience</strong> is the process and outcome of successfully adapting to difficult or challenging life experiences, especially through mental, emotional, and behavioral flexibility and adjustment to external and internal demands.</li>'
                .'<li><strong>Adaptability</strong> is a crucial life skill for people of all ages, as it enables individuals to respond effectively to unexpected changes and evolving demands.</li>'
                .'<li><strong>Growth Mindset</strong> is a mindset that emphasizes the potential for change and improvement, in contrast to the "Fixed Mindset," which holds that abilities are innate and unchangeable.</li>'
                .'</ul>'
                .'<p><strong>Why these concepts are important in local/rural youth work contexts:</strong></p>'
                .'<p>In today\'s world of constant change, uncertainty and increased demands, young people need skills that allow them to respond effectively to challenges and maintain their mental balance. This is particularly important in local and rural contexts where young people may have limited access to opportunities, services and support networks.</p>'
                .'<p><strong>Connection to WellMe project:</strong></p>'
                .'<p>The project aims, among other things, to support young people to discover their strengths, make positive choices and build a brighter future for themselves and their communities. The skills of resilience, adaptability and growth mindset are essential core skills for this goal.</p>'
            , $m->ID);

            update_field('module_conclusion',
                '<p><em>"It is not the thing or the situation that disturbs, but the opinion about the thing or the situation"</em>, Epictetus</p>'
                .'<p>Ἡ ἀρετὴ ἕξις ἐστίν — "Virtue is a habit," Aristotle said, meaning a permanent attitude and stable disposition of the soul that is acquired through conscious choice and repetition of actions. It is a moral quality that is achieved through habit. This is true for most skills. Through habit and repetition, we can acquire the skills and attitudes that help us navigate the modern and demanding way of life.</p>'
            , $m->ID);

            update_field('module_learning_outcomes', [
                [
                    'outcome_title' => 'Knowledge',
                    'outcome_detail' => '<p>Understanding that failure is a common part of personal and professional growth. Recognize real challenges of life in the countryside. Identify lessons from failure. Analyze the stages of recovery after a failure.</p>',
                ],
                [
                    'outcome_title' => 'Skills',
                    'outcome_detail' => '<p>Manage challenges or difficulties. Recognize strategies to overcome them. Develop resilience, perseverance, and adaptability. Create small realistic action plans.</p>',
                ],
                [
                    'outcome_title' => 'Attitudes',
                    'outcome_detail' => '<p>View failure as an opportunity for learning and growth. Strengthening a growth mindset and openness to personal development. Remain positive in difficult situations.</p>',
                ],
                [
                    'outcome_title' => 'Connection to Module Goals',
                    'outcome_detail' => '<p>This activity allows learners to learn from the failures of people who ultimately succeeded or not, demystifies failure and teaches them through a group activity that in life things do not go as planned. This results in them gaining self-confidence and increasing the skills of resilience and growth mindset.</p>'
                    .'<p>References: The activity is inspired by experiential learning approaches in adult education and reflective practice (Kolb, Mezirow), using anonymized narratives of well-known figures who experienced failure.</p>',
                ],
            ], $m->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title' => 'Group Exercise: Failure Stories (approx. 30 min)',
                    'step_content' => '<p>The trainer, after dividing the trainees into small groups of 4-5 people, distributes narrative stories that are at the end of this exercise or others of his/her own that he/she wants to utilize. He/she does not reveal at the beginning who the protagonist of the story is. The trainees in small groups read and study the story.</p>'
                    .'<p>Each group answers in writing: Where exactly is the failure in the story you read? What lesson/lessons can we take from this story? What skills does he/she need to deal with the condition he/she is experiencing?</p>'
                    .'<p>After each group presents the answers to the above to the plenary, the trainer introduces the protagonist of the story and a discussion follows about the challenges, failures and all that happens in everyone\'s life, but the skills we have developed such as resilience, adaptability, perseverance, and the willingness to work hard help us to respond.</p>',
                    'step_hotspot_x' => 25, 'step_hotspot_y' => 30,
                ],
                [
                    'step_title' => 'Discussion: "The Musician Who Became a Teacher" (5-10 min)',
                    'step_content' => '<p>The trainer shares the story "The musician who became a teacher" (from the appendix) and starts a discussion for 5-10 minutes.</p>'
                    .'<p>Are there people who ultimately failed to achieve their big dream? What lessons do we learn from the story you read? What skills do you identify?</p>',
                    'step_hotspot_x' => 50, 'step_hotspot_y' => 25,
                ],
                [
                    'step_title' => 'Individual Experiential Exercise (15 min + sharing)',
                    'step_content' => '<p>Each trainee has 15 minutes to write about a failure, a failure that made him/her question himself/herself. Not something small. Something that really hurt him/her, disappointed him/her, confused him/her.</p>'
                    .'<p>What happened next? Did he/she overcome it? How did he/she overcome it? What did he/she learn from this journey?</p>'
                    .'<p>After the 15 minutes, those of the trainees who want to — it is not mandatory for everyone to do so — share their story and all the lessons they learned with the plenary.</p>'
                    .'<p><strong>Important Note:</strong> The trainer does not comment on the story, he/she thanks the trainee for sharing.</p>',
                    'step_hotspot_x' => 75, 'step_hotspot_y' => 35,
                ],
            ], $m->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'What will we take away from today\'s activity?'],
                ['reflection_question' => 'What is helpful to remember about failure?'],
            ], $m->ID);

            update_field('module_table_of_contents', "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions", $m->ID);
            $log[] = '  -> OK';
        }

        // ==========================================
        // MODULE 3 - UNIZAR
        // ==========================================
        if ($n === 3) {
            update_field('module_subtitle', 'Healthy Nutrition & Lifestyle in Youth Work', $m->ID);
            update_field('module_description', 'This module addresses healthy nutrition, lifestyle balance, and holistic wellbeing as interconnected pillars of youth development. Key concepts include: diet culture (beliefs equating thinness with worth); media literacy (critically evaluating food and body-image content); body positivity and Health at Every Size; and the Health Belief Model (self-efficacy and perceived benefits driving behaviour change).', $m->ID);
            update_field('module_color', '#F39C12', $m->ID);
            update_field('module_motto', '"Your worth is not determined by your weight, your diet, or your feed – it is determined by who you are."', $m->ID);

            update_field('module_introduction',
                '<p><strong>What this module is about:</strong></p>'
                .'<p>This module addresses healthy nutrition, lifestyle balance, and holistic wellbeing as interconnected pillars of youth development. Key concepts include: diet culture (beliefs equating thinness with worth); media literacy (critically evaluating food and body-image content); body positivity and Health at Every Size; and the Health Belief Model (self-efficacy and perceived benefits driving behaviour change).</p>'
                .'<p>This module equips trainers with knowledge about nutrition, wellbeing, and lifestyle balance in youth development, connecting healthy habits with mental clarity and emotional stability. The module invites youth workers and community facilitators to reimagine their role in promoting sustainable health practices, particularly in local and rural communities.</p>'
                .'<p>"Good nutrition fuels not just the body, it fuels confidence, focus, and the courage to dream".</p>'
                .'<p><strong>Why these concepts are important in local/rural youth work contexts:</strong></p>'
                .'<p>In local and rural communities, young people often face limited access to fresh food, fewer structured wellbeing resources, and stronger exposure to processed food marketing. These concepts are critical because they equip youth workers to address structural inequalities while building individual agency. Empowerment-based nutrition education fosters ownership over health choices, strengthens community bonds, and supports mental wellbeing in contexts where professional services may be scarce.</p>'
                .'<p><strong>Connection to WellMe project:</strong></p>'
                .'<p>Module 3 directly advances the WellMe Wellbeing Hubs mission by treating nutrition and lifestyle as foundations of holistic youth wellbeing in local communities. It equips facilitators to create inclusive, participatory learning environments — the core of WellMe hubs — where young people develop sustainable health habits. The module\'s emphasis on community-based action, cultural sensitivity, and peer support mirrors WellMe\'s commitment to co-creating supportive ecosystems for youth flourishing.</p>'
            , $m->ID);

            update_field('module_conclusion',
                '<p>This exercise is consistently one of the most impactful in Module 3. Participants leave with heightened awareness of how commercial interests shape their self-image, and with practical tools to reclaim their digital spaces.</p>'
                .'<p>The "Self-Love Feed" list created during Phase 4 can serve as a living resource the group continues to build across sessions. As a closing motto: "Your worth is not determined by your weight, your diet, or your feed – it is determined by who you are." Encourage youth workers to follow up in subsequent sessions by asking participants to share one positive account they discovered and one harmful account they unfollowed.</p>'
            , $m->ID);

            update_field('module_learning_outcomes', [
                [
                    'outcome_title' => 'Knowledge',
                    'outcome_detail' => '<p>What diet culture is and how it operates through social media. How advertising constructs unrealistic body standards. Principles of body positivity and Health at Every Size. How online content shapes self-image and self-worth.</p>',
                ],
                [
                    'outcome_title' => 'Skills',
                    'outcome_detail' => '<p>Critical analysis of social media posts (intent, assumptions, target audience). Constructing counter-narratives. Curating healthier online environments. Group discussion and collaborative presentation.</p>',
                ],
                [
                    'outcome_title' => 'Attitudes',
                    'outcome_detail' => '<p>Greater self-compassion and body respect. Critical resistance to comparison culture and diet messaging. Openness to celebrating bodily diversity. A sense of agency over their digital spaces.</p>',
                ],
                [
                    'outcome_title' => 'Connection to Module Goals',
                    'outcome_detail' => '<p>The Social Media Detox exercise directly operationalises Module 3\'s theoretical framework on media literacy, body image and behaviour change. By deconstructing real social media content, participants apply the Health Belief Model and Social Cognitive Theory in an immediately relevant context. The exercise translates the module\'s critical stance on diet culture into a practical skill set, building resilience against harmful messaging and fostering the empowerment-based approach to health that is central to Module 3 and the WellMe project.</p>',
                ],
            ], $m->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title' => '1. Introduction & Social Media Audit (15-20 min)',
                    'step_content' => '<p>Open with discussion: "What food, fitness and body content do you see on social media daily? How does it make you feel?" Introduce the concept of diet culture: the belief that thinness equals health and worth.</p>',
                    'step_hotspot_x' => 20, 'step_hotspot_y' => 30,
                ],
                [
                    'step_title' => '2. Critical Analysis Activity (25-30 min)',
                    'step_content' => '<p>Show examples of common posts ("What I Eat in a Day" videos, detox-tea ads, before/after photos, digitally altered fitness influencers). Small groups analyse each post using: "What message is this sending? Who profits? Is this realistic? What assumptions does it make?" Groups share findings with the whole group.</p>',
                    'step_hotspot_x' => 45, 'step_hotspot_y' => 25,
                ],
                [
                    'step_title' => '3. Body Positivity & Health at Every Size Education (15-20 min)',
                    'step_content' => '<p>Present alternative perspectives: health is not determined by appearance alone; bodies are diverse and diversity is normal; healthy behaviours matter more than achieving a certain look. Show body-positive content creators and campaigns.</p>',
                    'step_hotspot_x' => 70, 'step_hotspot_y' => 35,
                ],
                [
                    'step_title' => '4. Creating a Healthier Feed (15-20 min)',
                    'step_content' => '<p>Participants: unfollow/mute accounts that make them feel bad; identify body-positive, science-based accounts to follow; create a group "Self-Love Feed" list; write personal positive affirmations or counter-narratives.</p>',
                    'step_hotspot_x' => 85, 'step_hotspot_y' => 28,
                ],
            ], $m->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => '"Which social media post analysed today had the strongest effect on you — and why? What does this tell you about how diet culture operates in your everyday digital life?"'],
                ['reflection_question' => '"Social Cognitive Theory tells us that we learn through observing others. How does the modelling of unrealistic body standards on social media influence what young people believe is "normal" or desirable? How can youth workers use positive role models as a counter-strategy?"'],
                ['reflection_question' => '"As a youth worker, what one specific action could you take in your community to help young people build a healthier relationship with social media and body image? What barriers might you face, and how could you address them?"'],
            ], $m->ID);

            update_field('module_table_of_contents', "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions", $m->ID);
            $log[] = '  -> OK';
        }

        // ==========================================
        // MODULE 4 - ETAP
        // ==========================================
        if ($n === 4) {
            update_field('module_subtitle', 'Integrating Physical Activity into Youth Wellbeing', $m->ID);
            update_field('module_description', 'This module uses physical activity as an accessible, evidence-based way to support youth wellbeing where services are limited. It reframes movement beyond sport into everyday and nature-based activities that build autonomy, competence and relatedness, and that support emotional regulation, attention, social skills and healthier daily routines in a warm, relational way.', $m->ID);
            update_field('module_color', '#3498DB', $m->ID);
            update_field('module_motto', '"Step by step, side by side: movement makes space for words the heart struggles to share sitting still."', $m->ID);

            update_field('module_introduction',
                '<p><strong>What this module is about:</strong></p>'
                .'<p>This module uses physical activity as an accessible, evidence-based way to support youth wellbeing where services are limited. It reframes movement beyond sport into everyday and nature-based activities that build autonomy, competence and relatedness, and that support emotional regulation, attention, social skills and healthier daily routines in a warm, relational way.</p>'
                .'<p><strong>Why these concepts are important in local/rural youth work contexts:</strong></p>'
                .'<p>Rural and local areas often lack formal mental health provision but have rich outdoor and community spaces such as village squares, school yards, seafronts and paths. By grounding wellbeing work in simple movement and nature-based activities in these familiar places, youth workers can offer low-cost, context-sensitive support that fits young people\'s real environments, reduces exclusion and gently strengthens their sense of belonging.</p>'
                .'<p><strong>Connection to WellMe project:</strong></p>'
                .'<p>Module 4 is directly linked with WellMe\'s aim to build "Wellbeing Hubs" as local learning environments for youth in small, local and rural communities. As part of the Hands-On Training Programme, it equips youth workers with structured, movement-based and nature-based sessions they can deliver through the Hubs, so that wellbeing becomes something young people experience in their bodies, relationships and everyday local spaces, not only as information.</p>'
            , $m->ID);

            update_field('module_conclusion',
                '<p>A motto that captures the spirit of this exercise and Module 4 is: "Step by step, side by side: movement makes space for words the heart struggles to share sitting still."</p>'
            , $m->ID);

            update_field('module_learning_outcomes', [
                [
                    'outcome_title' => 'Knowledge',
                    'outcome_detail' => '<p>By receiving a clear frame, prompts on stress/coping, and a debrief about walk-and-talk and green exercise, they learn that walking side-by-side in nature can lower social pressure, ease emotional expression and link movement with wellbeing.</p>',
                ],
                [
                    'outcome_title' => 'Skills',
                    'outcome_detail' => '<p>By pairing up, using the prompt cards, alternating speaker-listener roles and doing a grounding pause, they practise attentive listening, pacing their self-disclosure, and using simple body- and environment-focused techniques while walking.</p>',
                ],
                [
                    'outcome_title' => 'Attitudes',
                    'outcome_detail' => '<p>Through the closing reflection ("how was walking vs sitting?", "when could this help you?"), they are invited to see movement and outdoor places as gentle wellbeing resources and to recognise "walk & talk" as a respectful, low-threshold way to seek or offer support in their own lives.</p>',
                ],
                [
                    'outcome_title' => 'Connection to Module Goals',
                    'outcome_detail' => '<p>This exercise enacts the module\'s core idea that movement is a practical "language of wellbeing" for youth. It uses low-intensity walking in local outdoor routes to support emotional regulation, focused attention and social connection, directly targeting the psychological needs of autonomy (choosing pace and depth of sharing), competence (successfully engaging in a structured walk-and-talk) and relatedness (feeling heard and accompanied).</p>',
                ],
            ], $m->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title' => 'Preparation & Safety (5 min)',
                    'step_content' => '<p>Explain the purpose: walking in pairs to talk about what gives energy, what challenges them and how they cope; highlight that walking side-by-side can make talking easier. Give confidentiality and safety rules; clarify route, boundaries, meeting point, timing and staying within sight of the trainer.</p>',
                    'step_hotspot_x' => 15, 'step_hotspot_y' => 30,
                ],
                [
                    'step_title' => 'Pairing & Prompts (5 min)',
                    'step_content' => '<p>Form pairs (or occasional triads) and hand out 2-3 prompt questions per pair focused on energy, stress and supportive places.</p>',
                    'step_hotspot_x' => 35, 'step_hotspot_y' => 25,
                ],
                [
                    'step_title' => 'Walk & Talk (15-20 min)',
                    'step_content' => '<p>Begin the walk along the chosen route; invite pairs to alternate speaker and listener, allow silence, and remind them they can skip questions that feel too personal. Trainer walks at the back or moves between pairs, observing, available if needed but not joining conversations.</p>',
                    'step_hotspot_x' => 60, 'step_hotspot_y' => 35,
                ],
                [
                    'step_title' => 'Quiet Pause & Group Reflection (3-5 min)',
                    'step_content' => '<p>On return, invite 1-2 minutes of quiet standing or sitting with a brief grounding on breath, feet and surroundings. Close in a circle with voluntary sharing on how walking vs sitting felt, and what they notice in body and mood now; summarise key messages about walk-and-talk and green exercise for wellbeing.</p>',
                    'step_hotspot_x' => 85, 'step_hotspot_y' => 30,
                ],
            ], $m->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'How did walking side-by-side, instead of sitting face-to-face, change the way you spoke, listened, or felt during the conversation?'],
                ['reflection_question' => 'What did this exercise show you in practice about the link between movement, nature and emotional regulation that we discussed in the module?'],
                ['reflection_question' => 'As a youth worker, in which real situations (conflicts, check-ins, mentoring talks) could a short "walk & talk" be a safer, more accessible option than a formal sit-down talk?'],
            ], $m->ID);

            update_field('module_table_of_contents', "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions", $m->ID);
            $log[] = '  -> OK';
        }

        // ==========================================
        // MODULE 5 - CENTREDOT
        // ==========================================
        if ($n === 5) {
            update_field('module_subtitle', 'Designing Environments for Youth Belonging', $m->ID);
            update_field('module_description', 'This module explores how the design of spaces—community spaces, learning environments, youth centers, digital hubs—can foster a sense of belonging. What really creates connection is not just what the space looks like, but how it feels, who is included, and what it allows young people to become.', $m->ID);
            update_field('module_color', '#6C3483', $m->ID);
            update_field('module_motto', '«Ο άνθρωπος αγιάζει τον τόπο, κι όχι ο τόπος τον άνθρωπο»', $m->ID);

            update_field('module_introduction',
                '<p><strong>What this module is about:</strong></p>'
                .'<p>This module explores how the design of spaces—community spaces, learning environments, youth centers, digital hubs—can foster a sense of belonging. But we go beyond the bricks and mortar. What really creates connection is not just what the space looks like, but how it feels, who is included, and what it allows young people to become.</p>'
                .'<p>A space that promotes youth belonging is one where young people feel emotionally safe, culturally acknowledged, and socially connected. It\'s a place where they can speak freely, be themselves, and grow without fear of judgment.</p>'
                .'<p><strong>Why these concepts are important in local/rural youth work contexts:</strong></p>'
                .'<p>In every corner of our societies—urban or rural, digital or physical—young people are searching for something vital: a place to belong. Belonging is not a luxury; it is a foundational human need. For youth, it\'s the difference between feeling invisible and feeling valued, between disengagement and participation, between isolation and connection.</p>'
                .'<p>For example, a youth café that runs an open mic night every Friday gives teens the chance to share their poetry, rap, or personal stories—creating trust and recognition.</p>'
                .'<p><strong>Connection to WellMe project:</strong></p>'
                .'<p>This module directly contributes to the WellMe project by supporting the design of Wellbeing Hubs as inclusive learning environments. It empowers youth to explore, reflect on, and co-design spaces that enhance belonging and wellbeing. By engaging youth as active participants, the module strengthens social inclusion, promotes community engagement, and aligns with the project\'s goal of creating sustainable, youth-centered learning ecosystems in local communities.</p>'
            , $m->ID);

            update_field('module_conclusion',
                '<p>Το πλήρες γνωμικό είναι: «Ο άνθρωπος αγιάζει τον τόπο, κι όχι ο τόπος τον άνθρωπο».</p>'
                .'<p>Σημασία:</p>'
                .'<ul>'
                .'<li>Η ποιότητα της ζωής, η ατμόσφαιρα και το αποτέλεσμα μιας προσπάθειας εξαρτώνται από τους ανθρώπους που απαρτίζουν μια ομάδα ή κοινότητα, και όχι από την τοποθεσία ή τις υλικές συνθήκες.</li>'
                .'<li>Οι άνθρωποι με τις πράξεις, τον χαρακτήρα και τη διάθεσή τους μπορούν να ομορφύνουν («να αγιάσουν») οποιοδήποτε μέρος, όσο δύσκολο ή άσχημο κι αν είναι.</li>'
                .'<li>Αντίστοιχα, το αντίστροφο ισχύει επίσης: ακόμη και ο ωραιότερος τόπος μπορεί να γίνει αφιλόξενος αν οι άνθρωποι που βρίσκονται εκεί δεν είναι κατάλληλοι.</li>'
                .'</ul>'
            , $m->ID);

            update_field('module_learning_outcomes', [
                [
                    'outcome_title' => 'Develop awareness',
                    'outcome_detail' => '<p>Develop awareness of how physical environments influence emotions and sense of belonging.</p>',
                ],
                [
                    'outcome_title' => 'Strengthen skills',
                    'outcome_detail' => '<p>Strengthen observation, critical thinking and reflection skills.</p>',
                ],
                [
                    'outcome_title' => 'Build ability',
                    'outcome_detail' => '<p>Build ability to express opinions and share personal experiences in a group.</p>',
                ],
                [
                    'outcome_title' => 'Foster responsibility',
                    'outcome_detail' => '<p>Foster sense of responsibility and active citizenship towards local spaces.</p>',
                ],
                [
                    'outcome_title' => 'Connection to Module Goals',
                    'outcome_detail' => '<p>Designing such spaces means including young people in the process from the beginning. Belonging grows when Youth feel they are not guests, but co-owners of their space.</p>'
                    .'<p>This exercise directly supports the module\'s aim of helping youth understand how spaces shape belonging. By exploring their own environments, participants identify inclusive and exclusive elements and reflect on how spaces can be improved. It reinforces the idea of youth as co-designers of community spaces, strengthening their role in creating environments that support wellbeing and social inclusion.</p>',
                ],
            ], $m->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title' => 'Introduction (10-15 min)',
                    'step_content' => '<p>Introduce the concepts of place identity and belonging, explaining how spaces affect feelings and behaviour.</p>',
                    'step_hotspot_x' => 15, 'step_hotspot_y' => 30,
                ],
                [
                    'step_title' => 'Walking & Photo Collection (30-45 min)',
                    'step_content' => '<p>Ask participants to walk around their local area and take 4-5 photos:</p>'
                    .'<ul><li>2 places where they feel comfortable or connected</li><li>1-2 places they dislike or feel excluded from</li></ul>',
                    'step_hotspot_x' => 35, 'step_hotspot_y' => 25,
                ],
                [
                    'step_title' => 'Mapping & Sharing (30-45 min)',
                    'step_content' => '<p>Participants return and place photos (printed or digital) on a shared map or board.</p>'
                    .'<p>Facilitate discussion: What makes a place feel safe or welcoming? What makes it uncomfortable or excluded? Identify common patterns. Mark positive spaces (green) and negative spaces (red).</p>',
                    'step_hotspot_x' => 65, 'step_hotspot_y' => 35,
                ],
                [
                    'step_title' => 'Reflection (10-15 min)',
                    'step_content' => '<p>In small groups, reflect on how negative spaces could be improved and who could support these changes. Encourage participants to step further — design a development plan for the places they dislike.</p>',
                    'step_hotspot_x' => 85, 'step_hotspot_y' => 28,
                ],
            ], $m->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'Which place made you feel the strongest sense of belonging and why?'],
                ['reflection_question' => 'How did this activity change the way you see your community?'],
                ['reflection_question' => 'With whom will you engage with to achieve more inclusive spaces?'],
                ['reflection_question' => 'What small action could you take to improve a space in your area?'],
            ], $m->ID);

            update_field('module_table_of_contents', "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions", $m->ID);
            $log[] = '  -> OK';
        }

        // ==========================================
        // MODULE 6 - AUTOKREACJA
        // ==========================================
        if ($n === 6) {
            update_field('module_subtitle', 'Guiding Youth Transitions through Community Power', $m->ID);
            update_field('module_description', 'Module 6 explores how community assets, mentoring relationships and peer networks support young people (16-25) in transitions to adulthood. It treats adulthood as a social process shaped by access to opportunities, supportive relationships, real-life learning and participation. The module draws on asset-based community development, social capital, Positive Youth Development, social learning, self-determination and salutogenic approaches.', $m->ID);
            update_field('module_color', '#1ABC9C', $m->ID);
            update_field('module_motto', 'Strong transitions grow where young people, relationships and community resources meet.', $m->ID);

            update_field('module_introduction',
                '<p><strong>What this module is about:</strong></p>'
                .'<p>Module 6 explores how community assets, mentoring relationships and peer networks support young people (16-25) in transitions to adulthood. It treats adulthood as a social process shaped by access to opportunities, supportive relationships, real-life learning and participation. The module draws on asset-based community development, social capital, Positive Youth Development, social learning, self-determination and salutogenic approaches.</p>'
                .'<p><strong>Why these concepts are important in local/rural youth work contexts:</strong></p>'
                .'<p>In local and rural youth work, young people often face fewer services, limited mobility and weaker access to networks. These concepts help trainers see local people, places and organisations as assets, strengthen mentoring and peer support, and build practical pathways to education, work, belonging and wellbeing close to home.</p>'
                .'<p><strong>Connection to WellMe project:</strong></p>'
                .'<p>The module supports WellMe by showing how local wellbeing hubs can become practical ecosystems of support. It helps youth workers activate community resources, create safe and participatory learning spaces, and connect wellbeing with autonomy, inclusion, resilience and sustainable transitions to adult life.</p>'
            , $m->ID);

            update_field('module_conclusion',
                '<p>Motto: Strong transitions grow where young people, relationships and community resources meet.</p>'
            , $m->ID);

            update_field('module_learning_outcomes', [
                [
                    'outcome_title' => 'Knowledge',
                    'outcome_detail' => '<p>Community assets, mentoring and peer networks in youth transitions.</p>',
                ],
                [
                    'outcome_title' => 'Skills',
                    'outcome_detail' => '<p>Mapping local supports, analysing barriers and presenting findings.</p>',
                ],
                [
                    'outcome_title' => 'Attitudes',
                    'outcome_detail' => '<p>Inclusion, collaboration and strengths-based thinking.</p>',
                ],
                [
                    'outcome_title' => 'Connection to Module Goals',
                    'outcome_detail' => '<p>The exercise translates the module into practice: participants identify community assets, barriers and overlooked spaces, then reflect on how local actors can better support youth transitions. It directly reinforces community mapping, participation, real-life learning and connected support systems.</p>',
                ],
            ], $m->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title' => '1. Introduction (10 min)',
                    'step_content' => '<p>Introduce the task: explore the area from the perspective of a young person moving toward adulthood.</p>',
                    'step_hotspot_x' => 15, 'step_hotspot_y' => 30,
                ],
                [
                    'step_title' => '2. Neighbourhood Walk (45 min)',
                    'step_content' => '<p>In groups, identify places that feel supportive, familiar, inaccessible or overlooked. For each place, take one photo and record a short audio note explaining its meaning.</p>',
                    'step_hotspot_x' => 40, 'step_hotspot_y' => 25,
                ],
                [
                    'step_title' => '3. Map-Making (35 min)',
                    'step_content' => '<p>Return and draw a large community map; mark the places and add photos/QR codes if possible.</p>',
                    'step_hotspot_x' => 65, 'step_hotspot_y' => 35,
                ],
                [
                    'step_title' => '4. Presentations & Reflection (20 min)',
                    'step_content' => '<p>Each group presents its map and explains where support, opportunity or exclusion appears. Close with a debrief on barriers, assets and ideas for stronger local support.</p>',
                    'step_hotspot_x' => 85, 'step_hotspot_y' => 30,
                ],
            ], $m->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'Which places in your community most strongly support young people\'s transition to adulthood, and why?'],
                ['reflection_question' => 'How did the mapping activity help you connect theories such as community assets or social capital with real local experience?'],
                ['reflection_question' => 'What is one practical change a youth worker or local partner could make to strengthen community-based support for young people?'],
            ], $m->ID);

            update_field('module_table_of_contents', "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions", $m->ID);
            $log[] = '  -> OK';
        }
    }

    echo "<h1>All 6 Modules Fixed</h1><pre>" . implode("\n", $log) . "</pre><p><strong>DELETE THIS FILE NOW.</strong></p>";
    exit;
});
