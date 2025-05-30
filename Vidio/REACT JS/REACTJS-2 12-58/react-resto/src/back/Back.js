import React from 'react';
import { Routes, Route } from 'react-router-dom';
import Nav from './Nav';
import Side from './Side';
import Content from './Content';
import Footer from './Footer';
import Main from './Main';

export default function Back() {
  return (
    <div>
      <div className="row">
        <div>
          <Nav />
        </div>
      </div>
      <div className="row mt-2">
        <div className="col-md-3">
          <Side />
        </div>
        <div className="col-md-9">
          <Content />
        </div>
      </div>
      <div className="row mt-4">
        <div>
          <Footer />
        </div>
      </div>
    </div>
  );
}