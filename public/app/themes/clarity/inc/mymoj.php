<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) {
    die();
}

class MyMOJ
{

    public static function get_my_work_links($agency = 'hq')
    {
        $data = [];

        $menu_items = get_field($agency .'_my_work_links', 'option');

        if ($menu_items) {
            foreach ($menu_items as $menu_item) {
                $my_work_link['title'] = $menu_item['my_work_link_title'];
                $my_work_link['url'] = $menu_item['my_work_link_url'];
                $data[] = $my_work_link;
            }
        }

        return $data;
    }

    public static function get_apps($agency = 'hq')
    {
        $apps = [
        'judicial-office' => [
          [
            'title' => 'Contacts',
            'icon' => 'people-finder',
            'url' => 'https://intranet.justice.gov.uk/about-us-judicial-office/contacts/',
            'external' => false,
          ],
          [
            'title' => 'Jobs',
            'icon' => 'jobs',
            'url' => 'https://intranet.justice.gov.uk/jobs/',
            'external' => false,
          ],
          [
          'title' => 'Finance',
          'icon' => 'pension',
          'url' => 'https://intranet.justice.gov.uk/guidance/finance/',
          'external' => false,
          ],
          [
            'title' => 'IT Self Service Portal',
            'icon' => 'it-portal',
            'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
            'external' => true,
          ],
          [
            'title' => 'JO blog',
            'icon' => 'webchat',
            'url' => 'http://judicialoffice.blogs.justice.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'My Services',
            'icon' => 'civil-service-learning',
            'url' => 'https://myservices.justice.gov.uk/moj',
            'external' => true,
          ]
        ],
        'cica' => [
          [
            'title' => 'PLADS',
            'icon' => 'court-and-tribunal-finder',
            'url' => 'https://intranet.justice.gov.uk/policy-legal-decisions-support-guidance-homepage/',
            'external' => true,
          ],
          [
            'title' => 'People finder',
            'icon' => 'people-finder',
            'url' => 'https://peoplefinder.service.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'Civil Service Learning',
            'icon' => 'civil-service-learning',
            'url' => 'https://civilservicelearning.civilservice.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'SOP',
            'icon' => 'sop',
            'url' => site_url('/guidance/hr/sop/'),
            'external' => false,
          ],
          [
            'title' => 'Jobs',
            'icon' => 'jobs',
            'url' => site_url('/jobs/'),
            'external' => false,
          ],
          [
            'title' => 'Mitrefinch',
            'icon' => 'mitrefinch',
            'url' => 'http://cica-mitrefinch/tms/silverlight.aspx',
            'external' => true,
          ],
          [
            'title' => 'IT Self Service Portal',
            'icon' => 'it-portal',
            'url' => 'http://cicald-web/ServiceDesk.WebAccess/ss/Dashboard/OpenHomeDashboard.rails?id=c2492b87-2b72-4068-8983-0e1e2a2e6a15',
            'external' => true,
          ],
          [
            'title' => 'Bright Ideas',
            'icon' => 'bright-ideas',
            'url' => 'http://cicald-web/ServiceDesk.WebAccess/ss/object/createInCart.rails?class_name=RequestManagement.Request&lifecycle_name=NewProcess118&object_template_name=&attributes=_ConfigItemRequested-5aed3043-14a6-4091-874b-b6dbe1489fcd&RaiseUser-',
            'external' => true,
          ],

        ],
        'pb' => [
          [
            'title' => 'People finder',
            'icon' => 'people-finder',
            'url' => 'https://peoplefinder.service.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'SOP',
            'icon' => 'sop',
            'url' => site_url('/guidance/hr/sop/'),
            'external' => false,
          ],
          [
            'title' => 'Civil Service Learning',
            'icon' => 'civil-service-learning',
            'url' => 'https://civilservicelearning.civilservice.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'Jobs',
            'icon' => 'jobs',
            'url' => site_url('/jobs/'),
            'external' => false,
          ],
          [
            'title' => 'Parole Board public site',
            'icon' => 'govuk',
            'url' => 'https://www.gov.uk/government/organisations/parole-board',
            'external' => true,
          ],
        ],
        'law-commission' => [
          [
            'title' => 'People finder',
            'icon' => 'people-finder',
            'url' => 'https://peoplefinder.service.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'SOP',
            'icon' => 'sop',
            'url' => site_url('/guidance/hr/sop/'),
            'external' => false,
          ],
          [
            'title' => 'Room booking',
            'icon' => 'room-booking',
            'url' => 'https://app.matrixbooking.com/',
            'external' => true,
          ],
          [
            'title' => 'Travel booking',
            'icon' => 'travel-booking',
            'url' => 'https://www.trips.uk.com/js/SABS/Corporate.html',
            'external' => true,
          ],
          [
            'title' => 'IT portal',
            'icon' => 'it-portal',
            'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
            'external' => true,
          ],
          [
            'title' => 'Civil Service Learning',
            'icon' => 'civil-service-learning',
            'url' => 'https://civilservicelearning.civilservice.gov.uk/',
            'external' => true,
          ]
        ],
        'hq' => [
          [
            'title' => 'People finder',
            'icon' => 'people-finder',
            'url' => 'https://peoplefinder.service.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'Travel booking',
            'icon' => 'travel-booking',
            'url' => 'https://www.trips.uk.com/js/SABS/Corporate.html',
            'external' => true,
          ],
          [
            'title' => 'Jobs',
            'icon' => 'jobs',
            'url' => site_url('/jobs/'),
            'external' => false,
          ],
          [
            'title' => 'Pensions',
            'icon' => 'pension',
            'url' => 'http://www.civilservicepensionscheme.org.uk/',
            'external' => true,
          ],
          [
            'title' => 'SOP',
            'icon' => 'sop',
            'url' => site_url('/guidance/hr/sop/'),
            'external' => false,
          ],
          [
            'title' => 'Civil Service Learning',
            'icon' => 'civil-service-learning',
            'url' => 'https://civilservicelearning.civilservice.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'IT portal',
            'icon' => 'it-portal',
            'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
            'external' => true,
          ],
          [
            'title' => 'MoJ Webchat',
            'icon' => 'webchat',
            'url' => site_url('/webchats-hq/'),
            'external' => false,
          ],
          [
            'title' => 'Room booking',
            'icon' => 'room-booking',
            'url' => 'https://app.matrixbooking.com/',
            'external' => true,
          ],
          [
            'title' => 'Estates portal',
            'icon' => 'home',
            'url' => 'https://moj-portal.meandmyworkplace.com/?auth_token=9Q5Q42ybcao-KheuE31A4A',
            'external' => true,
          ],
        ],
        'hmcts' => [
          [
            'title' => 'SOP',
            'icon' => 'sop',
            'url' => site_url('/guidance/hr/sop/'),
            'external' => false,
          ],
          [
            'title' => 'Travel bookings (Redfern)',
            'icon' => 'travel-booking',
            'url' => site_url('/guidance/procurement/common-goods-services/travel/'),
            'external' => false,
          ],
          [
            'title' => 'Jobs',
            'icon' => 'jobs',
            'url' => site_url('/jobs/'),
            'external' => false,
          ],
          [
            'title' => 'IT portal',
            'icon' => 'it-portal',
            'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
            'external' => true,
          ],
          [
            'title' => 'Court and Tribunal Finder',
            'icon' => 'court-and-tribunal-finder',
            'url' => 'https://courttribunalfinder.service.gov.uk/search/',
            'external' => true,
          ],
          [
            'title' => 'Form Finder',
            'icon' => 'form-finder',
            'url' => 'https://www.gov.uk/court-and-tribunal-forms',
            'external' => true,
          ],
        ],
        'laa' => [
          [
            'title' => 'People finder',
            'icon' => 'people-finder',
            'url' => 'https://peoplefinder.service.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'Jobs',
            'icon' => 'jobs',
            'url' => site_url('/guidance/human-resources/internal-job-vacancies-2/'),
            'external' => false,
          ],
          [
            'title' => 'IT portal',
            'icon' => 'it-portal',
            'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
            'external' => true,
          ],
          [
            'title' => 'Civil Service Learning',
            'icon' => 'civil-service-learning',
            'url' => 'https://civilservicelearning.civilservice.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'LAA online portal',
            'icon' => 'laa-online-portal',
            'url' => 'https://portal.legalservices.gov.uk/LAAPortal/pages/home.jsp',
            'external' => true,
          ],
          [
            'title' => 'SOP',
            'icon' => 'sop',
            'url' => site_url('/guidance/hr/sop/'),
            'external' => false,
          ],
          [
            'title' => 'LAA manual (civil)',
            'icon' => 'laa-manual-civil',
            'url' => 'https://www.gov.uk/guidance/civil-legal-aid-civil-regulations-civil-contracts-and-guidance',
            'external' => true,
          ],
          [
            'title' => 'LAA manual (crime)',
            'icon' => 'laa-manual-crime',
            'url' => 'https://www.gov.uk/guidance/criminal-legal-aid-crime-regulations-crime-contracts-and-guidance',
            'external' => true,
          ],
          [
            'title' => 'Means Assessment Administration Tool',
            'icon' => 'maat',
            'url' => 'https://meansassessment.legalservices.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'Public LAA site',
            'icon' => 'govuk',
            'url' => 'https://www.gov.uk/government/organisations/legal-aid-agency',
            'external' => true,
          ],
          [
            'title' => 'Provider Information',
            'icon' => 'provider-information',
            'url' => site_url('/guidance/management-information-2/provider-information/'),
            'external' => false,
          ],
        ],
        'opg' => [
          [
            'title' => 'Etarmis',
            'icon' => 'etarmis',
            'url' => 'https://opg.hfxonline.co.uk/',
            'external' => true,
          ],
          [
            'title' => 'Jobs',
            'icon' => 'jobs',
            'url' => 'https://www.civilservicejobs.service.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'IT portal',
            'icon' => 'it-portal',
            'url' => 'http://itportal.dom1.infra.int:8080/Pages/default.aspx',
            'external' => true,
          ],
          [
            'title' => 'Civil Service Learning',
            'icon' => 'civil-service-learning',
            'url' => 'https://civilservicelearning.civilservice.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'SOP',
            'icon' => 'sop',
            'url' => site_url('/guidance/hr/sop/'),
            'external' => false,
          ],
          [
            'title' => 'OPG GOV.UK site',
            'icon' => 'govuk',
            'url' => 'https://www.gov.uk/government/organisations/office-of-the-public-guardian',
            'external' => true,
          ],
          [
            'title' => 'People finder',
            'icon' => 'people-finder',
            'url' => 'https://peoplefinder.service.gov.uk/',
            'external' => true,
          ],
          [
            'title' => 'Room booking',
            'icon' => 'room-booking',
            'url' => 'https://app.matrixbooking.com/',
            'external' => true,
          ],
        ]
        ];
        return $apps[$agency];
    }
}
