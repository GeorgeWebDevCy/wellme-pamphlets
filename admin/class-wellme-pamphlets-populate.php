<?php
/**
 * Temporary admin endpoint: populates WELLME module ACF fields from Word doc content.
 * DELETE this file after running once.
 */
add_action('admin_init', function () {
    if (!isset($_GET['wellme_populate_modules']) || $_GET['key'] !== 'populate2026') {
        return;
    }
    if (!function_exists('update_field')) {
        die('ACF not active');
    }
    if (!current_user_can('manage_options')) {
        die('Not authorized');
    }

    $modules = get_posts([
        'post_type'      => 'wellme_module',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'module_number',
        'order'          => 'ASC',
    ]);

    $log = [];

    foreach ($modules as $mod) {
        $num = (int) get_field('module_number', $mod->ID);
        $log[] = "Processing Module $num (ID: {$mod->ID}): " . get_the_title($mod);

        // --- Module 5: Spaces that Connect (most complete Word doc) ---
        if ($num === 5) {
            update_field('module_subtitle', 'Spaces that Connect: Designing Environments for Youth Belonging', $mod->ID);
            update_field('module_description', 'This module explores how the design of spaces — community spaces, learning environments, youth centers, digital hubs — can foster a sense of belonging.', $mod->ID);
            update_field('module_color', '#6C3483', $mod->ID);
            update_field('module_motto', 'Ο άνθρωπος αγιάζει τον τόπο, κι όχι ο τόπος τον άνθρωπο', $mod->ID);

            update_field('module_introduction',
                '<p>This module explores how the design of spaces — community spaces, learning environments, youth centers, digital hubs — can foster a sense of belonging. But we go beyond the bricks and mortar. What really creates connection is not just what the space looks like, but how it feels, who is included, and what it allows young people to become.</p>' .
                '<p>A space that promotes youth belonging is one where young people feel emotionally safe, culturally acknowledged, and socially connected. It is a place where they can speak freely, be themselves, and grow without fear of judgment.</p>' .
                '<p>For example, a youth café that runs an open mic night every Friday gives teens the chance to share their poetry, rap, or personal stories — creating trust and recognition.</p>' .
                '<p>This module directly contributes to the WellMe project by supporting the design of Wellbeing Hubs as inclusive learning environments. It empowers youth to explore, reflect on, and co-design spaces that enhance belonging and wellbeing.</p>',
                $mod->ID
            );

            update_field('module_conclusion',
                '<p>This module has explored the profound impact that space design has on youth belonging and wellbeing. Through the "My Place of Belonging" exercise, participants experienced firsthand how physical environments shape their emotional and social connections.</p>' .
                '<p>The key takeaway is that belonging is not a luxury — it is a foundational human need. When we involve young people in designing their own spaces, we empower them as active agents of their own wellbeing.</p>',
                $mod->ID
            );

            // Learning outcomes (Partou pattern)
            update_field('module_learning_outcomes', [
                [
                    'outcome_title'  => 'Awareness of Space & Emotion',
                    'outcome_detail' => '<p>Develop awareness of how physical environments influence emotions and sense of belonging. Understand that spaces are not neutral — they communicate messages about who belongs and who does not. A welcoming entrance, natural light, and inclusive signage all contribute to feelings of safety and acceptance.</p>',
                ],
                [
                    'outcome_title'  => 'Observation & Critical Thinking',
                    'outcome_detail' => '<p>Strengthen observation, critical thinking and reflection skills. Learn to analyze spaces through the eyes of different users — a teenager, a person with a disability, a newcomer to the community. Identify elements that invite participation and those that create barriers.</p>',
                ],
                [
                    'outcome_title'  => 'Expression & Group Discussion',
                    'outcome_detail' => '<p>Build ability to express opinions and share personal experiences in a group. Practice articulating how spaces make you feel, and listen to how others experience the same environments differently. This develops empathy and collaborative problem-solving.</p>',
                ],
                [
                    'outcome_title'  => 'Active Citizenship',
                    'outcome_detail' => '<p>Foster sense of responsibility and active citizenship towards local spaces. Understand that young people are not just users of spaces — they can be co-designers and advocates. Learn how to propose improvements and engage with local authorities to make change happen.</p>',
                ],
            ], $mod->ID);

            // Exercise steps (Outremer pattern)
            update_field('module_exercise_steps', [
                [
                    'step_title'   => 'Introduction (10-15 min)',
                    'step_content' => '<p>Introduce the concepts of place identity and belonging. Explain how spaces affect feelings and behaviour. Ask participants: <em>"Where do you feel most at home in your community? What makes it special?"</em></p><p>Set expectations for the outdoor walk and photo activity. Emphasize that there are no right or wrong answers.</p>',
                    'step_hotspot_x' => 20,
                    'step_hotspot_y' => 30,
                ],
                [
                    'step_title'   => 'Walking & Photo Collection (30-45 min)',
                    'step_content' => '<p>Ask participants to walk around their local area and take 4-5 photos:</p><ul><li>2 places where they feel comfortable or connected</li><li>1-2 places they dislike or feel excluded from</li></ul><p>Encourage them to think about why these spaces make them feel the way they do. What design elements contribute to their feelings?</p>',
                    'step_hotspot_x' => 45,
                    'step_hotspot_y' => 25,
                ],
                [
                    'step_title'   => 'Mapping & Sharing (30-45 min)',
                    'step_content' => '<p>Participants return and place photos (printed or digital) on a shared map or board. Facilitate discussion:</p><ul><li>What makes a place feel safe or welcoming?</li><li>What makes it uncomfortable or excluded?</li><li>Identify common patterns across the group</li></ul><p>Mark positive spaces (green stickers) and negative spaces (red stickers).</p>',
                    'step_hotspot_x' => 70,
                    'step_hotspot_y' => 35,
                ],
                [
                    'step_title'   => 'Reflection & Action Planning (10-15 min)',
                    'step_content' => '<p>In small groups, reflect on how negative spaces could be improved and who could support these changes.</p><p>Encourage participants to step further — design a development plan for the places they dislike. Who will you engage with to achieve more inclusive spaces?</p><p>Share one concrete action each participant will take.</p>',
                    'step_hotspot_x' => 85,
                    'step_hotspot_y' => 60,
                ],
            ], $mod->ID);

            // Reflection questions
            update_field('module_reflection_questions', [
                ['reflection_question' => 'Which place made you feel the strongest sense of belonging and why?'],
                ['reflection_question' => 'How did this activity change the way you see your community?'],
                ['reflection_question' => 'With whom will you engage with to achieve more inclusive spaces?'],
                ['reflection_question' => 'What small action could you take to improve a space in your area?'],
            ], $mod->ID);

            // Table of contents
            update_field('module_table_of_contents',
                "Introduction\nTheoretical Background\nModule Activity\nObjectives of the Activity\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions",
                $mod->ID
            );

            $log[] = "  -> Module 5 populated successfully";
        }

        // --- Module 1: From Strength To Strength ---
        if ($num === 1) {
            update_field('module_subtitle', 'Positive Psychology for Youth Trainers', $mod->ID);
            update_field('module_description', 'Positive Psychology looks at what helps people feel well, grow, and thrive. It focuses on wellbeing, personal strengths, and the conditions that support positive development.', $mod->ID);
            update_field('module_color', '#27AE60', $mod->ID);
            update_field('module_motto', 'Savor the Moment — gratitude turns what we have into enough', $mod->ID);

            update_field('module_introduction',
                '<p>Positive Psychology looks at what helps people feel well, grow, and thrive. It focuses on wellbeing, personal strengths, and the conditions that support positive development. This module equips trainers with knowledge about positive psychology principles and their application in youth work.</p>' .
                '<p>The key concepts include strengths-based practice, gratitude, mindfulness, and the conditions that support positive youth development. These concepts are important in local/rural youth work contexts because they provide practical tools that youth workers can use even with limited resources.</p>',
                $mod->ID
            );

            update_field('module_conclusion',
                '<p>This module has explored the foundations of positive psychology and their practical application in youth work. Through the gratitude practice exercise, participants experienced how focusing on positive moments can shift perspective and build resilience.</p>',
                $mod->ID
            );

            update_field('module_learning_outcomes', [
                [
                    'outcome_title'  => 'Understanding Positive Psychology',
                    'outcome_detail' => '<p>Learn the core principles of positive psychology and how they apply to youth development. Understand the difference between deficit-based and strengths-based approaches to working with young people.</p>',
                ],
                [
                    'outcome_title'  => 'Gratitude Practice Skills',
                    'outcome_detail' => '<p>Develop practical skills for facilitating gratitude exercises with youth groups. Learn how to create safe spaces for sharing positive experiences and how to build ongoing gratitude practices into regular programming.</p>',
                ],
                [
                    'outcome_title'  => 'Strengths-Based Mentoring',
                    'outcome_detail' => '<p>Strengthen the ability to identify and nurture young people\'s unique strengths. Practice techniques for helping youth recognize their own capabilities and build on them.</p>',
                ],
            ], $mod->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title'   => 'Opening & Warm-Up (10-15 min)',
                    'step_content' => '<p>Begin with a brief mindfulness exercise. Ask participants to close their eyes and recall a positive moment from the past week. What did they see, hear, and feel? This sets the tone for the gratitude practice.</p>',
                    'step_hotspot_x' => 20,
                    'step_hotspot_y' => 35,
                ],
                [
                    'step_title'   => 'Savor the Moment Exercise (30-40 min)',
                    'step_content' => '<p>Guide participants through a structured gratitude practice:</p><ul><li>Write down 3 things you are grateful for today</li><li>Share one with a partner and explain why it matters</li><li>Discuss: How does reflecting on gratitude change your mood?</li></ul>',
                    'step_hotspot_x' => 50,
                    'step_hotspot_y' => 30,
                ],
                [
                    'step_title'   => 'Group Discussion (15-20 min)',
                    'step_content' => '<p>In the full group, discuss how positive psychology techniques can be integrated into youth work programs. Brainstorm practical applications for each participant\'s own context.</p>',
                    'step_hotspot_x' => 75,
                    'step_hotspot_y' => 40,
                ],
            ], $mod->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'How did reflecting on a positive moment influence your mood or perspective?'],
                ['reflection_question' => 'How can you integrate gratitude practice into your regular youth work?'],
                ['reflection_question' => 'What strengths did you discover in the participants during this exercise?'],
            ], $mod->ID);

            update_field('module_table_of_contents',
                "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions",
                $mod->ID
            );

            $log[] = "  -> Module 1 populated successfully";
        }

        // --- Module 2: Bounce Back Stronger ---
        if ($num === 2) {
            update_field('module_subtitle', 'Resilience and Adaptability', $mod->ID);
            update_field('module_description', 'The cultivation of resilience, growth mindset, and adaptability represents a foundational triad in contemporary youth development and wellbeing education.', $mod->ID);
            update_field('module_color', '#E74C3C', $mod->ID);
            update_field('module_motto', 'Fall seven times, stand up eight — resilience is a practice', $mod->ID);

            update_field('module_introduction',
                '<p>The cultivation of resilience, growth mindset, and adaptability represents a foundational triad in contemporary youth development and wellbeing education. In an era where young people face unprecedented challenges — from academic pressure to social media comparison — the ability to bounce back from setbacks is more important than ever.</p>' .
                '<p>This module equips trainers with practical tools to help youth understand that failure is not the opposite of success, but a stepping stone toward it.</p>',
                $mod->ID
            );

            update_field('module_conclusion',
                '<p>This module has demonstrated that failure is not something to fear, but a valuable teacher. Through the storytelling exercise, participants discovered that the most successful people have failed many times — and that what matters is how we respond to setbacks.</p>',
                $mod->ID
            );

            update_field('module_learning_outcomes', [
                [
                    'outcome_title'  => 'Understanding Resilience',
                    'outcome_detail' => '<p>Learn what resilience really means — not avoiding difficulties, but developing the capacity to recover, adapt, and grow from challenges. Understand the neuroscience of stress response and how it can be managed.</p>',
                ],
                [
                    'outcome_title'  => 'Growth Mindset Development',
                    'outcome_detail' => '<p>Distinguish between fixed and growth mindsets. Practice reframing failures as learning opportunities. Develop techniques for helping young people shift from "I can\'t do this" to "I can\'t do this yet."</p>',
                ],
                [
                    'outcome_title'  => 'Storytelling for Resilience',
                    'outcome_detail' => '<p>Learn how to use storytelling as a tool for building resilience. Discover how sharing stories of failure and recovery helps normalize struggle and builds connection within groups.</p>',
                ],
            ], $mod->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title'   => 'Introduction to Failure Stories (15 min)',
                    'step_content' => '<p>The trainer divides trainees into small groups and shares stories of famous failures who eventually succeeded (e.g., Thomas Edison, J.K. Rowling, Michael Jordan). Discuss: What made the difference?</p>',
                    'step_hotspot_x' => 25,
                    'step_hotspot_y' => 30,
                ],
                [
                    'step_title'   => 'Personal Failure Sharing (25-30 min)',
                    'step_content' => '<p>In small groups, each participant shares a personal failure story and what they learned from it. The group identifies common themes: persistence, learning, adaptation, support from others.</p>',
                    'step_hotspot_x' => 55,
                    'step_hotspot_y' => 25,
                ],
                [
                    'step_title'   => 'Resilience Action Plan (20-25 min)',
                    'step_content' => '<p>Each participant creates a personal "Resilience Action Plan" — identifying their biggest current challenge, 3 possible responses, and 1 concrete step they will take this week.</p>',
                    'step_hotspot_x' => 80,
                    'step_hotspot_y' => 40,
                ],
            ], $mod->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'What will we take away from today\'s activity?'],
                ['reflection_question' => 'What is helpful to remember about failure?'],
                ['reflection_question' => 'How can we help young people see failure as a learning opportunity?'],
            ], $mod->ID);

            update_field('module_table_of_contents',
                "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions",
                $mod->ID
            );

            $log[] = "  -> Module 2 populated successfully";
        }

        // --- Module 3: Fuel for Flourishing ---
        if ($num === 3) {
            update_field('module_subtitle', 'Healthy Nutrition & Lifestyle in Youth Work', $mod->ID);
            update_field('module_description', 'This module addresses healthy nutrition, lifestyle balance, and holistic wellbeing as interconnected pillars of youth development.', $mod->ID);
            update_field('module_color', '#F39C12', $mod->ID);
            update_field('module_motto', 'Good nutrition fuels not just the body, it fuels confidence, focus, and the courage to dream', $mod->ID);

            update_field('module_introduction',
                '<p>This module addresses healthy nutrition, lifestyle balance, and holistic wellbeing as interconnected pillars of youth development. Key concepts include: diet culture (beliefs equating thinness with worth); media literacy (critically evaluating food and body-image content); body positivity and Health at Every Size.</p>' .
                '<p>In local and rural communities, young people often face limited access to fresh food, fewer structured wellbeing resources, and stronger exposure to processed food marketing. This module equips youth workers to address these challenges.</p>',
                $mod->ID
            );

            update_field('module_conclusion',
                '<p>This exercise is consistently one of the most impactful in Module 3. Participants leave with heightened awareness of how commercial interests shape their self-image, and with practical tools to reclaim their digital spaces.</p>' .
                '<p>As a closing motto: "Your worth is not determined by your weight, your diet, or your feed — it is determined by who you are."</p>',
                $mod->ID
            );

            update_field('module_learning_outcomes', [
                [
                    'outcome_title'  => 'Understanding Diet Culture',
                    'outcome_detail' => '<p>Learn what diet culture is and how it operates through social media. Understand how advertising constructs unrealistic body standards and affects youth self-image.</p>',
                ],
                [
                    'outcome_title'  => 'Critical Media Analysis',
                    'outcome_detail' => '<p>Strengthen critical analysis skills for social media posts — identifying intent, assumptions, and target audience. Practice deconstructing common messaging around food and body image.</p>',
                ],
                [
                    'outcome_title'  => 'Body Positivity & Health',
                    'outcome_detail' => '<p>Understand principles of body positivity and Health at Every Size. Develop strategies for creating healthier digital environments and supporting youth in building positive self-image.</p>',
                ],
            ], $mod->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title'   => 'Social Media Audit (15-20 min)',
                    'step_content' => '<p>Open with discussion: "What food, fitness and body content do you see on social media daily? How does it make you feel?" Introduce the concept of diet culture: the belief that thinness equals health and worth.</p>',
                    'step_hotspot_x' => 20,
                    'step_hotspot_y' => 30,
                ],
                [
                    'step_title'   => 'Critical Analysis Activity (25-30 min)',
                    'step_content' => '<p>Show examples of common posts ("What I Eat in a Day" videos, detox-tea ads, before/after photos). Small groups analyse each post: What message is this sending? Who profits? Is this realistic?</p>',
                    'step_hotspot_x' => 50,
                    'step_hotspot_y' => 25,
                ],
                [
                    'step_title'   => 'Creating a Healthier Feed (15-20 min)',
                    'step_content' => '<p>Participants unfollow/mute accounts that make them feel bad, identify body-positive accounts to follow, create a group "Self-Love Feed" list, and write personal positive affirmations.</p>',
                    'step_hotspot_x' => 80,
                    'step_hotspot_y' => 40,
                ],
            ], $mod->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'Which social media post analysed today had the strongest effect on you — and why?'],
                ['reflection_question' => 'How can youth workers use positive role models as a counter-strategy to unrealistic body standards?'],
                ['reflection_question' => 'What one specific action could you take in your community to help young people build a healthier relationship with social media?'],
            ], $mod->ID);

            update_field('module_table_of_contents',
                "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions",
                $mod->ID
            );

            $log[] = "  -> Module 3 populated successfully";
        }

        // --- Module 4: Move to Thrive ---
        if ($num === 4) {
            update_field('module_subtitle', 'Integrating Physical Activity into Youth Wellbeing', $mod->ID);
            update_field('module_description', 'This module uses physical activity as an accessible, evidence-based way to support youth wellbeing where services are limited.', $mod->ID);
            update_field('module_color', '#3498DB', $mod->ID);
            update_field('module_motto', 'Walk together, talk together — movement is the language of wellbeing', $mod->ID);

            update_field('module_introduction',
                '<p>This module uses physical activity as an accessible, evidence-based way to support youth wellbeing where services are limited. It reframes movement beyond sport into everyday wellbeing practice.</p>' .
                '<p>The Walk & Talk exercise combines gentle physical activity with meaningful conversation, creating a low-pressure environment where youth feel more comfortable opening up than in formal settings.</p>',
                $mod->ID
            );

            update_field('module_conclusion',
                '<p>Physical activity is one of the most accessible tools for supporting youth wellbeing. The Walk & Talk format breaks down barriers and creates natural opportunities for connection.</p>',
                $mod->ID
            );

            update_field('module_learning_outcomes', [
                [
                    'outcome_title'  => 'Movement as Wellbeing Tool',
                    'outcome_detail' => '<p>Understand how physical activity supports not just physical health, but mental clarity, emotional regulation, and social connection. Learn evidence for movement-based interventions in youth work.</p>',
                ],
                [
                    'outcome_title'  => 'Walk & Talk Facilitation',
                    'outcome_detail' => '<p>Develop skills for facilitating Walk & Talk sessions — choosing routes, pairing participants, managing pace, and guiding reflective conversations during movement.</p>',
                ],
                [
                    'outcome_title'  => 'Inclusive Activity Design',
                    'outcome_detail' => '<p>Learn to design physical activities that are inclusive of different abilities, fitness levels, and preferences. Understand adaptations for participants with limited mobility.</p>',
                ],
            ], $mod->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title'   => 'Preparation & Safety (5 min)',
                    'step_content' => '<p>Check the walking route for safety. Brief participants on the format: walk in pairs, discuss a prompt question, switch partners halfway. Ensure everyone has comfortable footwear.</p>',
                    'step_hotspot_x' => 20,
                    'step_hotspot_y' => 30,
                ],
                [
                    'step_title'   => 'First Walk & Talk (10 min)',
                    'step_content' => '<p>Pair participants. Prompt: "Share a challenge you are currently facing — what does it feel like in your body?" Walk for 5 minutes, then switch directions and partners.</p>',
                    'step_hotspot_x' => 50,
                    'step_hotspot_y' => 25,
                ],
                [
                    'step_title'   => 'Group Reflection (10-15 min)',
                    'step_content' => '<p>Gather the group. Ask: How did walking side-by-side change the conversation? What did you notice about your body during the walk? How did movement affect your mood?</p>',
                    'step_hotspot_x' => 80,
                    'step_hotspot_y' => 40,
                ],
            ], $mod->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'How did walking side-by-side, instead of sitting face-to-face, change the quality of your conversation?'],
                ['reflection_question' => 'How can you incorporate movement into your regular youth work practice?'],
                ['reflection_question' => 'What barriers might you face in getting youth to participate in physical activities, and how could you address them?'],
            ], $mod->ID);

            update_field('module_table_of_contents',
                "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions",
                $mod->ID
            );

            $log[] = "  -> Module 4 populated successfully";
        }

        // --- Module 6: Bridges to Adulthood ---
        if ($num === 6) {
            update_field('module_subtitle', 'Guiding Youth Transitions through Community Power', $mod->ID);
            update_field('module_description', 'Module 6 explores how community assets, mentoring relationships and peer networks support young people in transitions to adulthood.', $mod->ID);
            update_field('module_color', '#1ABC9C', $mod->ID);
            update_field('module_motto', 'Growing up is a journey — you don\'t have to walk it alone', $mod->ID);

            update_field('module_introduction',
                '<p>Module 6 explores how community assets, mentoring relationships and peer networks support young people (16-25) in transitions to adulthood. It treats adulthood as a process — not a destination — and examines how communities can serve as scaffolding for young people navigating this transition.</p>' .
                '<p>The Community Map exercise helps participants visualize the support systems available in their local area and identify gaps that need to be filled.</p>',
                $mod->ID
            );

            update_field('module_conclusion',
                '<p>Transitions to adulthood are complex and unique to each individual. By mapping community assets and understanding the support networks available, youth workers can better guide young people through this critical life stage.</p>',
                $mod->ID
            );

            update_field('module_learning_outcomes', [
                [
                    'outcome_title'  => 'Understanding Youth Transitions',
                    'outcome_detail' => '<p>Learn about the key dimensions of transitioning to adulthood — education, employment, housing, relationships, identity. Understand that transitions are non-linear and vary across cultures and communities.</p>',
                ],
                [
                    'outcome_title'  => 'Community Asset Mapping',
                    'outcome_detail' => '<p>Develop skills for mapping community assets that support youth transitions. Learn to identify formal resources (training programs, employment services) and informal ones (mentors, peer networks, community spaces).</p>',
                ],
                [
                    'outcome_title'  => 'Mentoring & Peer Support',
                    'outcome_detail' => '<p>Understand the different types of mentoring relationships and how peer networks can support youth transitions. Learn to facilitate peer support groups and mentoring connections.</p>',
                ],
            ], $mod->ID);

            update_field('module_exercise_steps', [
                [
                    'step_title'   => 'Introduction (10 min)',
                    'step_content' => '<p>Introduce the concept of community asset mapping. Explain that we will create a visual map of resources that support youth transitions in the local area.</p>',
                    'step_hotspot_x' => 20,
                    'step_hotspot_y' => 30,
                ],
                [
                    'step_title'   => 'Community Mapping (45 min)',
                    'step_content' => '<p>In groups, participants create a large map of their community, identifying: places that support youth (schools, youth centers, sports clubs), people who support youth (mentors, coaches, counselors), and gaps where support is missing.</p>',
                    'step_hotspot_x' => 50,
                    'step_hotspot_y' => 25,
                ],
                [
                    'step_title'   => 'Presentations & Discussion (30 min)',
                    'step_content' => '<p>Each group presents their map. Compare findings across groups. Identify common assets and gaps. Discuss: How can we fill the gaps? What role can youth workers play?</p>',
                    'step_hotspot_x' => 80,
                    'step_hotspot_y' => 40,
                ],
            ], $mod->ID);

            update_field('module_reflection_questions', [
                ['reflection_question' => 'Which places in your community most strongly support young people\'s transitions to adulthood?'],
                ['reflection_question' => 'What gaps did you identify, and how could they be addressed?'],
                ['reflection_question' => 'How can you strengthen mentoring relationships in your youth work practice?'],
            ], $mod->ID);

            update_field('module_table_of_contents',
                "Introduction\nTheoretical Background\nModule Activity\nObjectives\nStep-by-Step Guide\nTips for Trainers\nConclusion\nReflection Questions",
                $mod->ID
            );

            $log[] = "  -> Module 6 populated successfully";
        }
    }

    echo "<h1>WELLME Module Population Complete</h1>";
    echo "<pre>" . implode("\n", $log) . "</pre>";
    echo "<p><strong>DELETE this file now: " . __FILE__ . "</strong></p>";
    exit;
});
