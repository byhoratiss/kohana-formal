<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'admin_login' => array(
        'settings' => array('auto_validate' => false),
        
        'user' => array(
            'label' => 'Gebruikersnaam',
            'rules' => array('required' => true),
        ),
        'pass' => array(
            'label' => 'Wachtwoord',
            'rules' => array('required' => true)
        )
    ),
    
    'banner_file_upload' => array(
        
    ),

    'blog_post_create' => array(
        'title' => array(
            'label' => 'Titel',
            'rules' => array('required' => true, 'max' => 100)
        ),
        'post' => array(
            'label' => 'Post',
            'rules' => array('required' => true)
        ),
        'summary' => array(
            'label' => 'Samenvatting',
            'rules' => array('required' => true, 'max' => 255)
        )
    ),

    'blog_post_delete' => array(
        'delete' => array(
            'label' => 'Verwijderen',
            'rules' => array('required' => true)
        )
    ),
    
    'blog_post_edit' => array(
        'title' => array(
            'label' => 'Titel',
            'rules' => array('required' => true, 'max' => 100)
        ),
        'post' => array(
            'label' => 'Post',
            'rules' => array('required' => true)
        ),
        'summary' => array(
            'label' => 'Samenvatting',
            'rules' => array('required' => true, 'max' => 255)
        )
    ),
    
    'cms_page' => array(
        'title' => array(
            'label' => 'Titel',
            'rules' => array('required' => true)
        ),
        'slug' => array(
            'label' => 'Slug',
            'rules' => array('required' => true, 'max' => 100, 'regex' => '^([a-z0-9]+)(/([a-z0-9\-_]+))*$'),
            'messages' => array(
                'regex' => 'Pagina url niet in geldig formaat; gebruik alleen de tekens - / en geen hoofdletters'                
            )
        ),
        'body' => array(
            'label' => 'Pagina body',
            'rules' => array('required' => true)
        )
    ),
    
    'cms_page_delete' => array(
        'delete' => array(
            'label' => 'Verwijderen',
            'rules' => array('required' => true)
        )
    ),
    
    'cms_menu_reorder' => array(
        
    ),
    
    'edit_act' => array(
        'name' => array(
            'label' => 'Naam',
            'rules' => array('required' => true, 'min' => 3, 'max' => 100)
        ),
        'description' => array(
            'label' => 'Beschrijving',
            'rules' => array('required' => true, 'min' => 20)
        )
    ),
    
    'timetable_act_create' => array(
        'name' => array(
            'label' => 'Naam',
            'rules' => array('required' => true, 'max' => 100)
        ),
        'description' => array(
            'label' => 'Beschrijving',
            'rules' => array('required' => true)
        )
    ),
    
    'timetable_act_delete' => array(
        'delete' => array(
            'label' => 'Verwijderen',
            'rules' => array('required' => true)
        )
    ),
    
    'timetable_stage_create' => array(
        'title' => array(
            'label' => 'Naam',
            'rules' => array('required' => true)
        ),
        'location' => array(
            'label' => 'Locatie',
            'rules' => array('required' => true)
        )
    ),
    
    'timetable_stage_delete' => array(
        'delete' => array(
            'label' => 'Verwijderen',
            'rules' => array('required' => true)
        )
    ),
    
    'timetable_stage_edit' => array(
        'title' => array(
            'label' => 'Naam',
            'rules' => array('required' => true)
        ),
        'location' => array(
            'label' => 'Locatie',
            'rules' => array('required' => true)
        )
    ),
    
    'timetable_timetable_create' => array(
        'act' => array(
            'label' => 'Optreden',
            'rules' => array('numeric' => true, 'required' => true)
        ),
        'stage' => array(
            'label' => 'Podium',
            'rules' => array('numeric' => true, 'required' => true)
        ),
        'startdate' => array(
            'label' => 'Startdatum',
            'rules' => array('required' => true, 'date' => true)
        ),
        'enddate' => array(
            'label' => 'Einddatum',
            'rules' => array('required' => true, 'date' => true)
        ),
        'starthour' => array(
            'label' => 'Begin uur',
            'rules' => array('required' => true, 'min' => 16, 'max' => 23)
        ),
        'startminute' => array(
            'label' => 'Begin minuten',
            'rules' => array('required' => true, 'min' => 0, 'max' => '59')
        ),
        'endhour' => array(
            'label' => 'Eind uur',
            'rules' => array('required' => true, 'min' => 0, 'max' => 23)
        ),
        'endminute' => array(
            'label' => 'Eind minuten',
            'rules' => array('required' => true, 'min' => 0, 'max' => 59)
        )
    ),
    
    'timetable_timetable_delete' => array(
        'delete' => array(
            'label' => 'Verwijderen',
            'rules' => array('required' => true)
        )
    ),
    
    'timetable_timetable_edit' => array(
        'act' => array(
            'label' => 'Optreden',
            'rules' => array('numeric' => true, 'required' => true)
        ),
        'stage' => array(
            'label' => 'Podium',
            'rules' => array('numeric' => true, 'required' => true)
        ),
        'startdate' => array(
            'label' => 'Startdatum',
            'rules' => array('required' => true, 'date' => true)
        ),
        'enddate' => array(
            'label' => 'Einddatum',
            'rules' => array('required' => true, 'date' => true)
        ),
        'starthour' => array(
            'label' => 'Begin uur',
            'rules' => array('required' => true, 'min' => 16, 'max' => 23)
        ),
        'startminute' => array(
            'label' => 'Begin minuten',
            'rules' => array('required' => true, 'min' => 0, 'max' => '59')
        ),
        'endhour' => array(
            'label' => 'Eind uur',
            'rules' => array('required' => true, 'min' => 0, 'max' => 23)
        ),
        'endminute' => array(
            'label' => 'Eind minuten',
            'rules' => array('required' => true, 'min' => 0, 'max' => 59)
        )
    ), 
);