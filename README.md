# Manual Tech Test

## Important Note
Question 3 in the provided questionnaire spreadsheet suggests that not having (or having had) a heart or neurological condition would preclude any product recommendations. The seeders for this project have shifted the exclusions to the "Yes" answer.

## Installation
1. Clone the respository: `git clone git@github.com:michaeljmeadows/manual.git`
1. Copy `.env.example` to `.env` and enter your database details.This solution will work with either a MySQL or SQLite configuration
1. Install Composer dependencies: `composer install`
1. Run `php artisan key:generate` to add an application key to your project
1. Install npm dependencies: `npm install`
1. Migrate your database: `php artisan migrate:fresh --seed`
1. Start your local server: `php artisan serve`

## Solutions

### 1 - Returning the questionnaire.
GET: `localhost:8000/api/questionnaire`

This will return the complete questionnaire, including all questions and answers, sorted in the correct order. Answers will point to the next question, where applicable. This is achieved using an injected repository that caches the questionnaire data to minimise database queries.

### 2 - Evaluating the questionnaire answers.
POST: `localhost:8000/api/questionnaire/answers`

This endpoint accepts a JSON object with a single key, "answers", which must be an array of objects keyed with "questionId" and "answerId". All question-answer pairs must represent a complete set of questionnaire answers - answers need not be supplied for every question in the system. For ease, an answer starter kit has been supplied:

```
{
    "answers": [
        {
            "questionId": 1, // 1. Do you have difficulty getting or maintaining an erection?
            "answerId": 1 // Yes
            //"answerId": 2 // No
        },
        {
            "questionId": 2, // 2. Have you tried any of the following treatments before?
            "answerId": 3 // Viagra or Sildenafil
            //"answerId": 4 // Cialis or Tadalafil
            //"answerId": 5 // Both
            //"answerId": 6 // None of the above
        },
        {
            "questionId": 3, // 2a. Was the Viagra or Sildenafil product you tried before effective?
            "answerId": 7 // Yes
            //"answerId": 8 // No
        },
        {
            "questionId": 4, // 2b. Was the Cialis or Tadalafil product you tried before effective?
            "answerId": 9 // Yes
            //"answerId": 10 // No
        },
        {
            "questionId": 5, // 2c. Which is your preferred treatment?
            "answerId": 11 // Viagra or Sildenafil
            //"answerId": 12 // Cialis or Tadalafil
            //"answerId": 13 // None of the above
        },
        {
            "questionId": 6, // 3. Do you have, or have you ever had, any heart or neurological conditions?
            //"answerId": 14 // Yes
            "answerId": 15 // No 
        },
        {
            "questionId": 7, // 4. Do any of the listed medical conditions apply to you?
            //"answerId": 16 // Significant liver problems (such as cirrhosis of the liver) or kidney problems
            //"answerId": 17 // Currently prescribed GTN, Isosorbide mononitrate, Isosorbide dinitrate, Nicorandil (nitrates) or Rectogesic ointment
            //"answerId": 18 // Abnormal blood pressure (lower than 90/50 mmHg or higher than 160/90 mmHg)
            //"answerId": 19 // Condition affecting your penis (such as Peyronie's Disease, previous injuries or an inability to retract your foreskin)
            "answerId": 20 // I don't have any of these conditions
        },
        {
            "questionId": 8, // 5. Are you taking any of the following drugs?
            //"answerId": 21 // Alpha-blocker medication such as Alfuzosin, Doxazosin, Tamsulosin, Prazosin, Terazosin or over-the-counter Flomax
            //"answerId": 22 // Riociguat or other guanylate cyclase stimulators (for lung problems)
            //"answerId": 23 // Saquinavir, Ritonavir or Indinavir (for HIV)
            //"answerId": 24 // Cimetidine (for heartburn)
            "answerId": 25 // I don't take any of these drugs
        }
    ]
}
```

This will return the recommended products as listed in the spreadsheet (except for the adjustment mentioned in the Important Note). Processing is handled by an injected action class that uses the repository from the previous solution.

By structuring the system this way, both the retrieval of the questionnaire and the evaluation of answers will have minimal impact on the database, limited to just five queries per day to refresh the cache.

## Testing
An initial testing suite has been included, which should be configured to use a test database:
1. Copy your `.env` file to `.env.testing` and update the database details to use your testing database (MySQL or SQLite).
1. Migrate the test database: `php artisan migrate:fresh --env=testing`
1. Run the tests with: `php artisan test`
