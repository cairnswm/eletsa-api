# Comments

Discourage comments in source files.

# Context and Data

Do not allow data fetching excpept inside useEffects. A useEffect must call a fetchData function to fetch the data.

Do not fetch data inside components. All data fetching and changes must happen in a React Context.

When wroking with collections of data, add an active<Item> to the context so that we can make an item active and fetch the relevant related data whenever it chnages.

# General

Function name should be camel caseFiles should not be longer than 150 lines of code. If longer consider breaking into smaller components.
Modals should always be in their own files