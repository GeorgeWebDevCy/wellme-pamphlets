<?php
/**
 * Assessment helpers for WELLME modules.
 *
 * @since      1.0.6
 * @package    Wellme_Pamphlets
 * @subpackage Wellme_Pamphlets/includes
 */

class Wellme_Pamphlets_Assessment {

    /**
     * Get structured assessment questions for a module.
     *
     * @param int $module_id Module post ID.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function get_module_questions( $module_id ) {
        return self::normalize_questions( get_field( 'module_assessment_questions', $module_id ) ?: [] );
    }

    /**
     * Normalize repeater-based questions coming from ACF.
     *
     * @param array $questions Raw ACF rows.
     *
     * @return array<int,array<string,mixed>>
     */
    private static function normalize_questions( array $questions ) {
        $normalized = [];

        foreach ( $questions as $question ) {
            $prompt      = trim( wp_strip_all_tags( $question['assessment_question_prompt'] ?? '' ) );
            $options     = self::normalize_options( $question['assessment_question_options'] ?? [] );
            $correct_key = strtoupper( trim( wp_strip_all_tags( $question['assessment_question_correct_option'] ?? '' ) ) );
            $explanation = trim( wp_strip_all_tags( $question['assessment_question_explanation'] ?? '' ) );

            if ( '' === $prompt || empty( $options ) ) {
                continue;
            }

            if ( '' === $correct_key || ! isset( $options[ $correct_key ] ) ) {
                continue;
            }

            $normalized[] = [
                'prompt'         => $prompt,
                'options'        => $options,
                'correct_option' => $correct_key,
                'explanation'    => $explanation,
            ];
        }

        return $normalized;
    }

    /**
     * Normalize nested option rows from ACF.
     *
     * @param array $options Raw option rows.
     *
     * @return array<string,string>
     */
    private static function normalize_options( array $options ) {
        $normalized = [];

        foreach ( $options as $index => $option ) {
            $fallback_key = chr( 65 + $index );
            $key          = strtoupper( trim( wp_strip_all_tags( $option['option_key'] ?? $fallback_key ) ) );
            $text         = trim( wp_strip_all_tags( $option['option_text'] ?? '' ) );

            if ( '' === $text ) {
                continue;
            }

            $normalized[ $key ] = $text;
        }

        return $normalized;
    }

    /**
     * Extract assessment questions from legacy chapter HTML.
     *
     * @param array $chapters Chapter repeater rows.
     *
     * @return array<int,array<string,mixed>>
     */
    private static function extract_questions_from_chapters( array $chapters ) {
        $assessment_html = '';
        $answers_html    = '';

        foreach ( $chapters as $chapter ) {
            $title   = strtolower( trim( wp_strip_all_tags( $chapter['chapter_title'] ?? '' ) ) );
            $content = (string) ( $chapter['chapter_content'] ?? '' );

            if ( '' === $content ) {
                continue;
            }

            if ( false !== strpos( $title, 'assessment' ) ) {
                $assessment_html .= "\n" . $content;
            } elseif ( false !== strpos( $title, 'answer' ) ) {
                $answers_html .= "\n" . $content;
            }
        }

        if ( '' === trim( $assessment_html ) ) {
            return [];
        }

        return self::parse_assessment_html( $assessment_html, self::parse_answer_map( $answers_html ) );
    }

    /**
     * Parse HTML tables into structured assessment questions.
     *
     * @param string $assessment_html Assessment chapter HTML.
     * @param array  $answer_map      Parsed answer key by question number.
     *
     * @return array<int,array<string,mixed>>
     */
    private static function parse_assessment_html( $assessment_html, array $answer_map ) {
        $questions = [];
        $current   = null;

        foreach ( self::extract_tables( $assessment_html ) as $rows ) {
            if ( empty( $rows ) ) {
                continue;
            }

            $question = self::parse_question_table( $rows );

            if ( $question ) {
                if ( $current ) {
                    $finalized = self::finalize_question( $current, $answer_map );

                    if ( $finalized ) {
                        $questions[] = $finalized;
                    }
                }

                $current = $question;

                if ( self::table_has_answer_prompt( $rows ) ) {
                    $finalized = self::finalize_question( $current, $answer_map );

                    if ( $finalized ) {
                        $questions[] = $finalized;
                    }

                    $current = null;
                }

                continue;
            }

            if ( $current ) {
                $options = self::extract_options_from_rows( $rows );

                if ( ! empty( $options ) ) {
                    $current['options'] = array_merge( $current['options'], $options );
                }

                if ( self::table_has_answer_prompt( $rows ) ) {
                    $finalized = self::finalize_question( $current, $answer_map );

                    if ( $finalized ) {
                        $questions[] = $finalized;
                    }

                    $current = null;
                }
            }
        }

        if ( $current ) {
            $finalized = self::finalize_question( $current, $answer_map );

            if ( $finalized ) {
                $questions[] = $finalized;
            }
        }

        return $questions;
    }

    /**
     * Convert a pending question buffer into frontend data.
     *
     * @param array $question   Pending question.
     * @param array $answer_map Answer map keyed by question number.
     *
     * @return array<string,mixed>
     */
    private static function finalize_question( array $question, array $answer_map ) {
        $number       = (int) ( $question['number'] ?? 0 );
        $prompt       = trim( (string) ( $question['prompt'] ?? '' ) );
        $options      = $question['options'] ?? [];
        $answer_entry = $answer_map[ $number ] ?? [];
        $correct_key  = strtoupper( trim( (string) ( $answer_entry['correct_option'] ?? '' ) ) );

        if ( '' === $prompt || empty( $options ) || '' === $correct_key || ! isset( $options[ $correct_key ] ) ) {
            return [];
        }

        return [
            'prompt'         => $prompt,
            'options'        => $options,
            'correct_option' => $correct_key,
            'explanation'    => trim( (string) ( $answer_entry['explanation'] ?? '' ) ),
        ];
    }

    /**
     * Parse a question table.
     *
     * @param array $rows Table rows.
     *
     * @return array<string,mixed>
     */
    private static function parse_question_table( array $rows ) {
        $number  = 0;
        $prompt  = '';
        $options = [];

        foreach ( $rows as $row ) {
            $first_cell = isset( $row[0] ) ? self::clean_text( $row[0] ) : '';

            if ( '' === $first_cell ) {
                continue;
            }

            if ( 0 === $number ) {
                $question_data = self::parse_question_prompt( $first_cell );

                if ( $question_data ) {
                    $number = $question_data['number'];
                    $prompt = $question_data['prompt'];
                    continue;
                }
            }

            $option = self::parse_option_row( $row );

            if ( $option ) {
                $options[ $option['key'] ] = $option['text'];
            }
        }

        if ( 0 === $number || '' === $prompt ) {
            return [];
        }

        return [
            'number'  => $number,
            'prompt'  => $prompt,
            'options' => $options,
        ];
    }

    /**
     * Parse answer key rows.
     *
     * @param string $answers_html Answers chapter HTML.
     *
     * @return array<int,array<string,string>>
     */
    private static function parse_answer_map( $answers_html ) {
        $answer_map = [];

        foreach ( self::extract_tables( $answers_html ) as $rows ) {
            foreach ( $rows as $row ) {
                $first_cell = isset( $row[0] ) ? self::clean_text( $row[0] ) : '';
                $second_cell = isset( $row[1] ) ? self::clean_text( $row[1] ) : '';

                if ( ! preg_match( '/^Q\s*(\d+)(?:\s*[-: ]\s*([A-Z]))?$/i', $first_cell, $matches ) ) {
                    continue;
                }

                $correct_option = strtoupper( trim( (string) ( $matches[2] ?? '' ) ) );
                $explanation    = $second_cell;

                if ( '' === $correct_option && '' !== $second_cell ) {
                    if ( preg_match( '/^Correct answer\s*[:\-]?\s*([A-Z])\b\s*(.*)$/i', $second_cell, $second_matches ) ) {
                        $correct_option = strtoupper( $second_matches[1] );
                        $explanation    = self::clean_text( $second_matches[2] );
                    }
                }

                if ( '' === $correct_option ) {
                    continue;
                }

                $answer_map[ (int) $matches[1] ] = [
                    'correct_option' => $correct_option,
                    'explanation'    => $explanation,
                ];
            }
        }

        return $answer_map;
    }

    /**
     * Extract option rows from a table.
     *
     * @param array $rows Table rows.
     *
     * @return array<string,string>
     */
    private static function extract_options_from_rows( array $rows ) {
        $options = [];

        foreach ( $rows as $row ) {
            $option = self::parse_option_row( $row );

            if ( $option ) {
                $options[ $option['key'] ] = $option['text'];
            }
        }

        return $options;
    }

    /**
     * Parse an individual option row.
     *
     * @param array $row Table row cells.
     *
     * @return array<string,string>
     */
    private static function parse_option_row( array $row ) {
        $cells = array_values(
            array_filter(
                array_map( [ __CLASS__, 'clean_text' ], $row ),
                static function ( $value ) {
                    return '' !== $value;
                }
            )
        );

        if ( count( $cells ) >= 2 && preg_match( '/^[A-Z]$/', $cells[0] ) ) {
            return [
                'key'  => $cells[0],
                'text' => $cells[1],
            ];
        }

        if ( empty( $cells ) ) {
            return [];
        }

        if ( preg_match( '/^([A-Z])[\)\.\-:]?\s+(.+)$/', $cells[0], $matches ) ) {
            return [
                'key'  => strtoupper( $matches[1] ),
                'text' => trim( $matches[2] ),
            ];
        }

        return [];
    }

    /**
     * Parse a question prompt row.
     *
     * @param string $text Row text.
     *
     * @return array<string,mixed>
     */
    private static function parse_question_prompt( $text ) {
        if ( ! preg_match( '/^Q\s*(\d+)[\.\-:\)]?\s*(.+)$/i', $text, $matches ) ) {
            return [];
        }

        return [
            'number' => (int) $matches[1],
            'prompt' => trim( $matches[2] ),
        ];
    }

    /**
     * Check whether a table contains the legacy "Your answer" placeholder row.
     *
     * @param array $rows Table rows.
     *
     * @return bool
     */
    private static function table_has_answer_prompt( array $rows ) {
        foreach ( $rows as $row ) {
            foreach ( $row as $cell ) {
                if ( false !== stripos( self::clean_text( $cell ), 'your answer' ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Extract tables from an HTML fragment as row/cell arrays.
     *
     * @param string $html HTML fragment.
     *
     * @return array<int,array<int,array<int,string>>>
     */
    private static function extract_tables( $html ) {
        $tables = [];

        if ( ! preg_match_all( '/<table\b[^>]*>.*?<\/table>/is', (string) $html, $matches ) ) {
            return $tables;
        }

        foreach ( $matches[0] as $table_html ) {
            $rows = self::extract_rows_from_table( $table_html );

            if ( ! empty( $rows ) ) {
                $tables[] = $rows;
            }
        }

        return $tables;
    }

    /**
     * Extract rows from one table fragment.
     *
     * @param string $table_html Table HTML.
     *
     * @return array<int,array<int,string>>
     */
    private static function extract_rows_from_table( $table_html ) {
        if ( class_exists( 'DOMDocument' ) ) {
            $dom = new DOMDocument();

            libxml_use_internal_errors( true );
            $dom->loadHTML(
                '<?xml encoding="utf-8" ?><html><body>' . $table_html . '</body></html>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
            libxml_clear_errors();

            $rows = [];

            foreach ( $dom->getElementsByTagName( 'tr' ) as $row_node ) {
                $cells = [];

                foreach ( $row_node->childNodes as $cell_node ) {
                    if ( ! in_array( $cell_node->nodeName, [ 'td', 'th' ], true ) ) {
                        continue;
                    }

                    $cells[] = self::clean_text( $cell_node->textContent );
                }

                if ( ! empty( $cells ) ) {
                    $rows[] = $cells;
                }
            }

            if ( ! empty( $rows ) ) {
                return $rows;
            }
        }

        $rows = [];

        if ( ! preg_match_all( '/<tr\b[^>]*>(.*?)<\/tr>/is', $table_html, $row_matches ) ) {
            return $rows;
        }

        foreach ( $row_matches[1] as $row_html ) {
            if ( ! preg_match_all( '/<(?:td|th)\b[^>]*>(.*?)<\/(?:td|th)>/is', $row_html, $cell_matches ) ) {
                continue;
            }

            $cells = [];

            foreach ( $cell_matches[1] as $cell_html ) {
                $cells[] = self::clean_text( $cell_html );
            }

            if ( ! empty( $cells ) ) {
                $rows[] = $cells;
            }
        }

        return $rows;
    }

    /**
     * Normalize whitespace and strip markup from cell text.
     *
     * @param string $value Raw cell text.
     *
     * @return string
     */
    private static function clean_text( $value ) {
        $text = wp_strip_all_tags( html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' ) );

        return trim( preg_replace( '/\s+/u', ' ', $text ) );
    }
}
