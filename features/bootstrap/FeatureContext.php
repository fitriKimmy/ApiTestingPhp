<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected $response = null;
    protected $username = null;
    protected $password = null;
    protected $client = null;
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($github_username, $github_password)
    {
        $this->username = $github_username;
        $this->password = $github_password;
    }

    /**
     * @Given I am an anonymous user
     */
    public function iAmAnAnonymousUser()
    {
        return true;
    }

    /**
     * @When I search for :arg1
     */
    public function iSearchForBehat($arg1)
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.github.com']);
        $this->response = $client->get('/search/repositories?q='.$arg1);
    }

     /**
     * @Then I expect :arg1 response code
     */
    public function iExpectResponseCode($arg1)
    {
        $response_code = $this->response->getStatusCode();
        if($response_code != $arg1){
            throw new Exception("Failed, Expected $arg1 response code, Actual  $response_code".$arg1 .$response_code);
        }
    }

    /**
     * @Then I expect at least :arg1 result
     */
    public function iExpectAtLeastResult($arg1)
    {
        $data = $this->getBodyAsJson();
        if($data['total_count'] < $arg1){
            throw new Exception("We expect at least $arg1 result, Actual ".$data['total_count']);
        }
    }

    /**
     * @Given I am an authenticated user
     */
    public function iAmAnAuthenticatedUser()
    {
       $this->client = new GuzzleHttp\Client([
           'base_uri' => 'https://api.github.com',
           'auth' => [$this->username, $this->password]
       ]);
       $this->response = $this->client->get('/');
       $this->iExpectResponseCode(200);

    }

    /**
     * @When I request a list of my repositories
     */
    public function iRequestAListOfMyRepositories()
    {
        $this->response = $this->client->get('/user/repos');
        $this->iExpectResponseCode(200);
    }

    /**
     * @Then The result should include a repository called :arg1
     */
    public function theResultShouldIncludeARepositoryCalled($arg1)
    {
        $repositories = $this->getBodyAsJson();
        foreach($repositories as $repository){
            if($repository['name'] == $arg1) {
                return true;
            }
        }
        throw new Exception("Expected repository not found");
    }


     /**
     * @When I create :arg1 repository
     */
    public function iCreateRepository($arg1)
    {
        $parameters = json_encode(['name'=> $arg1]);
        $this->client-> post('/user/repos',['body' => $parameters]);
        $this->iExpectResponseCode(200);
    }

      /**
     * @Given I have a repository called :arg1
     */
    public function iHaveARepositoryCalled($arg1)
    {
        $this->iRequestAListOfMyRepositories();
        $this->theResultShouldIncludeARepositoryCalled($arg1);
    }

    /**
     * @When I watch the :arg1 repository
     */
    public function iWatchTheRepository($arg1)
    {
        $url = '/repos/' . $this->username . '/' .$arg1. '/subscription';
        $body = json_encode(['subscribed'=> 'true']);
        $this->client->put($url,['body' => $body]);
    }

    /**
     * @Then The :arg1 repository will  list me as a watcher
     */
    public function theRepositoryWillListMeAsAWatcher($arg1)
    {
        $url = '/repos/' . $this->username . '/' . $arg1 . '/subscribers';
        $this->response = $this->client->get($url);
        
        $subscribers = $this->getBodyAsJson();
        foreach($subscribers as $subscriber) {
            if($subscriber['login'] == $this->username){
                return true;
            }else{
                throw new Exception("You are not listed as a subsciber");
            }
        }
    }

    /**
     * @Then I delete the repository called :arg1
     */
    public function iDeleteTheRepositoryCalled($arg1)
    {
        $url = '/repos/' . $this->username . '/' .$arg1;
        $this->response = $this->client->delete($url);
        $this->iExpectResponseCode(204);
    }

    /**
     * @Given I have the following repositories:
     */
    public function iHaveTheFollowingRepositories(TableNode $table)
    {
        $this->table = $table->getRows();
        array_shift($this->table);

        foreach($this->table as $id => $row){
            $this->response = $this->client->get('/repos/' .$row[0]. '/' . $row[1]);
            $this->iExpectResponseCode(200);
        }
    }

    /**
     * @When I watch each repository
     */
    public function iWatchEachRepository()
    {
        $parameters = json_encode(['subscribed' => 'true']);
        foreach($this->table as $row){
            $url = '/repos/' . $row[0]. '/' .$row[1] .'/subscription';
            $this->client ->put($url, ['body' => $parameters]);
        }
    }

    /**
     * @Then My watch list will include those repositories
     */
    public function myWatchListWillIncludeThoseRepositories()
    {
        $watch_url = '/users/' . $this->username . '/subscriptions';
        $this->response = $this->client->get($watch_url);
        $watches = $this->getBodyAsJson();

        foreach($this->table as $row){
            $full_name = $row['name'];
            foreach($watches as $watch) {
               if($full_name != $watch['full_name']){
                   break 2;
               } 
            }
            throw new Exception("Error " .$this->username. "is not watching " .$full_name);
        }
    }

    protected function getBodyAsJson(){
        return json_decode($this->response->getBody(), true);
    }
}
