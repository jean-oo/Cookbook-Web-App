# project_a8g3b_u6e2b_v1z2b, recipe

If we have more time, we want to add some functions that allow user to collect their favorite recipes, and rates the recipes that they have viewed. 
Also, to be able to post their own recipes for others to review and share.

## Setup
- The files are stored in school server under \public_html.

- Giving permission to new file:
    `chmod 755 <filename>`

- Connect to school server using SSH: 
  Set Host as remote.students.cs.ubc.ca
  Set Port as 22
  Set Local Port as 1522
  Set Username as <CWL_Username>

## Optimization
- Password Security:
  Because the data was built by running the database (final).sql, we are unable to hash the password in the backend.
  If the project allows, we should hash the password and compare it in the database system to avoid being hacked. Less content should be stored in memory to reduce security risks.
- SQL injection prevention:
  We should use prepared statements to prevent SQL injection. 
  For example, in the login page, we should use the following code to prevent SQL injection:
  ```
  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
  $stmt->bind_param("ss", $username, $password);
  $stmt->execute();
  ```
