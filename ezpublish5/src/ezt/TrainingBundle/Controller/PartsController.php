<?php

namespace ezt\TrainingBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class PartsController extends Controller 
{
    public function getNavAction() {

	$query = new Query();
	$query->criterion = new Criterion\LogicalAnd(
	    array(
		new Criterion\ParentLocationId( 2 ),
                new Criterion\ContentTypeIdentifier(
		     array( 'folder', 'landing_page', 'blog', 'feedback_form' )
	        )
            )
        );

	// $this->getRepository()
        // $this->container->get( 'ezpublish.api.repository' )
	$searchResult = $this->getRepository()->getSearchService()->findContent( $query );

	$listLocation = array();
        if ( $searchResult->totalCount > 0 )
        {
	    foreach ( $searchResult->searchHits as $result )
	    {
		$listLocation[] = $this->getRepository()
				       ->getLocationService()
				       ->loadLocation( 
			     $result->valueObject->contentInfo->mainLocationId
                                      );
	    }
        }

	return $this->render(
	    "eztTrainingBundle::part_navigation.html.twig",
	    array(
		"listLocation" => $listLocation
            )
        );
    }


    public function getLastArticlesAction( $locationId )
    {

	$location = $this->getRepository()
                         ->getLocationService()
                         ->loadLocation( $locationId );
        $query = new Query();
	$query->criterion = new Criterion\LogicalAnd( 
	    array(
		new Criterion\Subtree( $location->pathString ),
		new Criterion\ContentTypeIdentifier( 'article' )	
	    )
	);
	$query->sortClauses = array(
	     new SortClause\DatePublished( Query::SORT_DESC )
        );
	$query->limit = 5;

	$searchResult = $this->getRepository()
			     ->getSearchService()
			     ->findContent( $query );

	return $this->render(
	   "eztTrainingBundle:render:last_articles.html.twig",
	   array( "searchResult" => $searchResult )
	);
    }
}
