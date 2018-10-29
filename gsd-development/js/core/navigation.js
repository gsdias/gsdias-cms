import React from "react";
import ReactDOM from "react-dom";

class Navigation extends React.Component {
  render() {
    const { active } = this.props;
    const { i18n, IS_ADMIN, IS_EDITOR, menu } = window.GSD;

    return (
      <>
        <span className="menu">Menu</span>
        <ul>
        {menu.map(item => {
          if (item.PERMISSION) {
            const className = `fa ${item.ICON} fa-2x`;
           return <li key={item.URL} className={item.ACTIVE == item.URL ? "active" : ""}>
            <a href={item.URL} title="Layouts">
              <i className={className} />
              {i18n[item.NAME]}
            </a>
          </li>;
          }
        })}
        </ul>
      </>
    );
  }
}

ReactDOM.render(<Navigation active={window.activeSection} />, document.getElementById("navigation"));
