<?php
add_filter( 'gettext', function( $translated_text, $text, $domain ) {
    // Map original strings to their replacements
    $replace = [
        'Messages' => 'Messages Change',
        'Search conversations' => 'Search conversations Change',
        'New message'    => 'New message change',
        'Start a conversation' => 'Start a conversation Change',
        'Search for someone to send them a direct message'  => 'Change',
        'Search for someone...' => 'Search for someone change...',
        'No conversations yet' => 'No conversations yet change',
        'Start a new conversation to get started' => 'Start a new conversation to get started change',
        'Type a message...' => 'Type a message change',
        'Reply to message' => 'Reply to message change',
        'Original Text 3' => 'My New Text 3',
        // Add more as needed
    ];
    if ( isset( $replace[ $text ] ) ) {
        return $replace[ $text ];
    }
    return $translated_text;
}, 20, 3 );