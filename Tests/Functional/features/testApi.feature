Feature: Api testing

	@api @add
	Scenario: Add an image to the favourites
		Given an image's url
		When call the api to add an image
		Then I should get a valid JSON with a response true

	@api @add
	Scenario: Add a duplicate image to the favourites
		Given i add an image's url
		And i give the same image's url
		When call the api to add the image
		Then I should get a valid JSON with a response false

	@api @add
	Scenario: Add a wrong image's url to the favourites
		Given a wrong image's url
		When call the api to add an image
		Then I should get a valid JSON with a response false
		But a message saying "The url provided is not valid"

	@api @add
	Scenario: Don't send any parameter to the add to favourites
		When call the api to add an image without an url
		Then I should get a valid JSON with a response false
		But a message saying "No POST parameter named url found"

	@api @get
	Scenario: Get List of Favoruites
		When I call /favourites on the api
		Then I should get an array in JSON
		And it must contain an "id", "url" and "description"

	@api @update @edit
	Scenario: Edit description of a favourite image
		Given i add an image's url
		And I call /Favourites on the api
		When I call PUT /favourite/id on the api
		Then I should get a valid JSON with a response true

	@api @update @edit
	Scenario: Edit description of a non existing image
		When I call PUT /favourite/id on the api with a wrong id
		Then I should get a valid JSON with a response false
		But a message saying "Something went wrong"

	@api @delete
	Scenario: Delete an image
		Given i add an image's url
		And I call /Favourites on the api
		When I call DELETE /favourite/id on the api
		Then I should get a valid JSON with a response true

	@api @delete
	Scenario: Delete a non existing image
		When I call DELETE /favourite/id on the api with a wrong id
		Then I should get a valid JSON with a response false
		But a message saying "Something went wrong"
