import {NgModule} from '@angular/core';
import {MatSnackBar, MatSnackBarModule} from '@angular/material/snack-bar';
import {ApolloModule, APOLLO_OPTIONS} from 'apollo-angular';
import {HttpLinkModule, HttpLink} from 'apollo-angular-link-http';
import {InMemoryCache} from 'apollo-cache-inmemory';
import {ApolloLink} from 'apollo-link';
import {setContext} from 'apollo-link-context';
import {onError} from "apollo-link-error";
import {ServerError, ServerParseError} from 'apollo-link-http-common';


const uri = 'https://fe36spzuvvhnjmbwaikbq77gda.appsync-api.eu-west-1.amazonaws.com/graphql'; // <-- add the URL of the GraphQL server here
const apiKey = 'da2-koxzbf4jyrg6lcboblfhx3pl2a';

export function createApollo(httpLink: HttpLink, snackbar: MatSnackBar) {
  const basic = setContext((operation, context) => ({
    headers: {
      Accept: 'charset=utf-8'
    }
  }));

  const auth = setContext((operation, context) => ({
    headers: {
      'x-api-key': `${apiKey}`
    },
  }));

  const errorLink = onError((error) => {
    console.log(error);
    if (error.networkError) {
      const networkError = <Error | ServerError | ServerParseError>error.networkError;
      // @ts-ignore
      if (networkError && networkError.statusCode === 401) {

      }
    }
    if (error.graphQLErrors.length) {
      snackbar.open(error.graphQLErrors[0].message, null, {
        duration: 3000,
      });
    }
  });

  const link = ApolloLink.from([basic, auth, errorLink.concat(httpLink.create({uri}))]);
  const cache = new InMemoryCache();

  return {
    link,
    cache
  };

  // return {
  //   link: httpLink.create({uri}),
  //   cache: new InMemoryCache(),
  // };
}

@NgModule({
  exports: [ApolloModule, HttpLinkModule],
  providers: [
    {
      provide: APOLLO_OPTIONS,
      useFactory: createApollo,
      deps: [HttpLink, MatSnackBar],
    },
  ],
})
export class GraphQLModule {
}
